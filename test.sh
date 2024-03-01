#!/bin/bash

php bin/console doctrine:database:drop -n --force --env=test
php bin/console doctrine:database:create -n --env=test
php bin/console doctrine:migrations:migrate -n --env=test
php bin/console doctrine:fixtures:load -n --env=test

php bin/phpunit $@
