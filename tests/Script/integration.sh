#!/bin/bash
bash /var/www/app/tests/Script/Helpers/reloadTestDatabase.sh
php /var/www/app/bin/phpunit --configuration /var/www/app/phpunit.xml --testsuite Integration
