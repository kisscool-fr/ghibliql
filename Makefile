# -*- mode: makefile -*-

COMPOSE = docker-compose
DOCKER_MACHINE = docker-machine
DOCKER_HTTP = httpd
DOCKER_APP = php
ROOT = /var/www/ghibliql
COMPOSER = composer
PHPUNIT = ./vendor/bin/phpunit
CSFIXER = ./vendor/bin/php-cs-fixer
STAN = ./vendor/bin/phpstan

.PHONY: run
run:
	$(COMPOSE) up

build:
	$(COMPOSE) rm -vsf
	$(COMPOSE) down -v --remove-orphans
	$(COMPOSE) build $(DOCKER_APP)

pull:
	$(COMPOSE) pull	

dev:
	$(COMPOSE) run $(DOCKER_APP) bash -c "cd $(ROOT) && $(COMPOSER) install --optimize-autoloader"

prod:
	$(COMPOSE) run $(DOCKER_APP) bash -c "cd $(ROOT) && $(COMPOSER) install --no-dev --optimize-autoloader --classmap-authoritative"

docker-start:
	$(DOCKER_MACHINE) start
	$(DOCKER_MACHINE) env default --shell cmd
	@echo "Docker running on DOCKER_HOST=$(DOCKER_HOST)"

docker-stop:
	@echo "Stopping Docker on DOCKER_HOST=$(DOCKER_HOST)"
	$(DOCKER_MACHINE) env --unset --shell cmd
	$(DOCKER_MACHINE) stop

jumpin:
	$(COMPOSE) run $(DOCKER_APP) bash

stan: dev
	$(COMPOSE) run $(DOCKER_APP) bash -c "cd $(ROOT) && $(STAN) analyse --configuration=phpstan.neon"

style: dev
	$(COMPOSE) run $(DOCKER_APP) bash -c "cd $(ROOT) && $(CSFIXER) fix lib --dry-run --diff-format=udiff --diff"

test: dev
	$(COMPOSE) run $(DOCKER_APP) bash -c "cd $(ROOT) && $(PHPUNIT)"

down:
	$(COMPOSE) down --volumes

clean:
	$(COMPOSE) down --volumes --rmi=all
