"""Exercise the SMS command against a local HTTP server with dummy credentials."""
import hashlib
import json
import subprocess
import sys
import tempfile
import threading
import unittest
from http.server import BaseHTTPRequestHandler, HTTPServer
from pathlib import Path
from urllib.parse import parse_qs, urlsplit

ROOT = Path(__file__).resolve().parents[1]


class SmsCommand(unittest.TestCase):
    def run_case(self, code=200, accepted=(), errors=(), statuses=(), malformed=False):
        calls = []
        states = iter(statuses)

        class Handler(BaseHTTPRequestHandler):
            def reply(self, code, body):
                self.send_response(code)
                self.end_headers()
                self.wfile.write(body)

            def do_POST(self):
                calls.append(self.path)
                self.rfile.read(int(self.headers['Content-Length']))
                data = dict(items=[dict(uuid=u, to='sample') for u in accepted],
                            errors=list(errors), success=bool(accepted))
                if code != 200:
                    data['error_message'] = 'Example error'
                self.reply(code, b'not JSON' if malformed else json.dumps(data).encode())

            def do_GET(self):
                path = urlsplit(self.path)
                fields = parse_qs(path.query)['fields'][0]
                signature = hashlib.md5((fields + self.headers['ts'] + 'dummy').encode()).hexdigest()
                if signature != self.headers['sig']:
                    self.reply(401, b'{}')
                    return
                calls.append(path.path)
                data = dict(item=dict(uuid=path.path.split('/')[-1], status=next(states), errCode=0))
                self.reply(200, json.dumps(data).encode())

            def log_message(self, *args):
                pass

        with HTTPServer(('127.0.0.1', 0), Handler) as server, tempfile.TemporaryDirectory() as tmp:
            url = 'http://127.0.0.1:' + str(server.server_port)
            thread = threading.Thread(target=server.serve_forever, kwargs={'poll_interval': 0.01})
            thread.start()
            try:
                from shutil import copytree
                dest = Path(tmp)
                copytree(ROOT / 'samples', dest / 'samples')
                copytree(ROOT / 'Redsms', dest / 'Redsms')
                (dest / 'config.php').write_text(
                    "<?php return ['login'=>'example', 'apiKey'=>'dummy', 'phone'=>'sample', "
                    "'smsSenderName'=>'example', 'apiUrl'=>'" + url + "'];")
                command = ['php', str(dest / 'samples/smsSendsample.php')]
                result = subprocess.run(command, capture_output=True, text=True, timeout=25)
            finally:
                server.shutdown()
                thread.join()
        self.assertEqual(calls.count('/message'), 1)
        return result, calls

    def test_request_errors(self):
        for code in (400, 401, 404, 420, 500):
            with self.subTest(code=code):
                result, calls = self.run_case(code, errors=[dict(to='sample', message='details')])
                self.assertNotEqual(result.returncode, 0)
                for value in (str(code), 'Example error', 'details'):
                    self.assertIn(value, result.stdout + result.stderr)
                self.assertEqual(calls, ['/message'])

    def test_invalid_response(self):
        result, _ = self.run_case(500, malformed=True)
        self.assertNotEqual(result.returncode, 0)
        self.assertIn('500', result.stdout + result.stderr)

    def test_partial_acceptance(self):
        result, calls = self.run_case(accepted=['one', 'two'], errors=[dict(to='invalid', message='bad number')],
                                      statuses=['delivered', 'reject'])
        self.assertNotEqual(result.returncode, 0)
        self.assertEqual(calls, ['/message', '/message/one', '/message/two'])
        self.assertIn('bad number', result.stderr)
        self.assertIn('reject', result.stdout)

    def test_delivery_statuses(self):
        for status in ('delivered', 'no_money', 'reject', 'undelivered', 'doubled', 'limit_exceeded',
                       'bad_number', 'stop_list', 'route_closed', 'timeout', 'error'):
            with self.subTest(status=status):
                result, calls = self.run_case(accepted=['one'], statuses=[status])
                self.assertEqual(result.returncode == 0, status == 'delivered')
                self.assertEqual(calls, ['/message', '/message/one'])
                self.assertIn(status, result.stdout)

    def test_pending_is_polled_once_more(self):
        for first, second in [('created', 'moderation'), ('progress', 'delivered')]:
            result, calls = self.run_case(accepted=['one'], statuses=[first, second])
            self.assertEqual(result.returncode, 0, result.stderr)
            self.assertEqual(calls, ['/message', '/message/one', '/message/one'])
            self.assertEqual('again later' in result.stdout, second == 'moderation')


if __name__ == '__main__':
    unittest.main()
