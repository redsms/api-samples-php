**Simple api wrapper for https://cp.redsms.ru service**

For use the service you need to register and specify api-key in the settings
https://cp.redsms.ru/settings/


**Run this samples**
#### Configure first
```
cp config.php.dist config.php 
```
##### set your data in config.php and run following command
```
 php -f samples/clientInfoSample.php
```

**Test this sample with docker and docker-compose**
```
docker-compose run php php -f samples/clientInfoSample.php
```