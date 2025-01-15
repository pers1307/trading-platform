DC := docker compose
PHP_EXEC := exec php php -dxdebug.mode=off

build:
	$(DC) build

build-no-cache:
	$(DC) build --no-cache

up:
	$(DC) up -d

stop:
	$(DC) stop

down:
	$(DC) down

clean: stop
	$(DC) rm -fv

in:
	$(DC) exec php bash

migrations-diff:
	$(DC) $(PHP_EXEC) bin/console doctrine:migrations:diff

migrations-up:
	$(DC) $(PHP_EXEC) bin/console doctrine:migrations:migrate

migrations-down:
	$(DC) $(PHP_EXEC) bin/console doctrine:migrations:migrate prev

cache-clear:
	$(DC) $(PHP_EXEC) bin/console cache:clear
