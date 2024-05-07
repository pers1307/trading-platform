#!/bin/bash

php bin/phpunit --coverage-text --testsuite Unit
php bin/phpunit --coverage-text --testsuite Integration
php bin/phpunit --coverage-text --testsuite Functional
