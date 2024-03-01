#!/bin/bash

# Написать некое конфигурирование перед запуском тестов!
php bin/console doctrine:database:drop -n --force --env=test
php bin/console doctrine:database:create -n --env=test
php bin/console doctrine:migrations:migrate -n --env=test
php bin/console doctrine:fixtures:load -n --env=test


php bin/phpunit --coverage-text --testsuite Unit
php bin/phpunit --coverage-text --testsuite Integration
php bin/phpunit --coverage-text --testsuite Functional

php bin/phpunit --testsuite DictionaryStock

# --coverage-text
# --filter CalculationTest
