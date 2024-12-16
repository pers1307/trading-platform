DC := docker compose

build:
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
