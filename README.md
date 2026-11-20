# RED SMS · PHP

Нужны PHP CLI и расширение curl. Из корня репозитория:

```sh
cp config.php.dist config.php
php -m
```

Убедитесь, что в списке есть `curl`. Внесите в `config.php` логин кабинета, API-ключ, свой номер и зарегистрированные имена отправителей.

```sh
php samples/clientInfoSample.php
php samples/senderNamesList.php
```

Для отправки одного платного SMS выполните `php samples/smsSendsample.php`. Скрипт выведет UUID принятого сообщения и запросит его статус. Конечный статус может появиться позже.

[Документация HTTP API](https://docs.redsms.ru/http/)
