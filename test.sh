#!/bin/bash
bash /var/www/app/tests/Script/Helpers/reloadTestDatabase.sh
php bin/phpunit $@
