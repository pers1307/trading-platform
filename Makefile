DC := docker compose

build:
	$(DC) build

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

server-start:
	$(DC) exec php symfony serve -d

server-stop:
	$(DC) exec php symfony server:stop
