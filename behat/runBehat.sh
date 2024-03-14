#!/bin/bash
#export BEHAT_PARAMS='{"extensions":{"Behat\\MinkExtension":{"base_url":"http://127.0.0.1:8000"}}}'
#export BEHAT_PARAMS='{"extensions" : {"Behat\\MinkExtension" : {"base_url" : "https://www.example.com/"}}}'

php vendor/bin/behat $@
