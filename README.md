# Simple api wrapper for [redsms](https://cp.redsms.ru) service

To use the service you need to register and specify API key in the [settings](https://cp.redsms.ru/settings/).

## Configuration

Copy the sample configuration file.

```
cp config.php.dist config.php
```

Put your API key and other data in `config.php`

## Testing API settings

Run an example local

```
 php -f samples/clientInfoSample.php
```

Run an example inside `docker` from the `samples` directory

```
docker-compose run php php -f samples/clientInfoSample.php
```