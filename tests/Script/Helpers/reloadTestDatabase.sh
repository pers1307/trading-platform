#!/bin/bash

php /var/www/app/bin/console doctrine:database:drop -n --force --env=test
php /var/www/app/bin/console doctrine:database:create -n --env=test
php /var/www/app/bin/console doctrine:migrations:migrate -n --env=test
php /var/www/app/bin/console doctrine:fixtures:load -n --env=test
