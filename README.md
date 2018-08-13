# Simple api wrapper for [redsms](https://cp.redsms.ru) service

To use the service you need to register and specify API key in the [settings](https://cp.redsms.ru/settings/).

## Configuration

copy the sample configuration file

```
cp config.php.dist config.php
```

put your API key and other data in config.php and run the following command

```
 php -f samples/clientInfoSample.php
```

## Testing the setup

```
docker-compose run php php -f samples/clientInfoSample.php
```