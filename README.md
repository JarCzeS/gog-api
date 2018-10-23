# gog-api
**set up** 

`composer install`

make .env and phpunit.xml from .dist 

fill database access in those files
, create database - `gog` and `gog_test`

`bin/console doctrine:schema:create`

`bin/console doctrine:fixtures:load --append --no-interaction`

launch symfony web server: `php -S 127.0.0.1:8000 -t public`

test endpoints(`bin/console debug:router`) in postman, run tests

