# -*- mode: makefile -*-

COMPOSE = docker compose
DOCKER_HTTP = httpd
DOCKER_APP = php
COMPOSER = composer
PHPUNIT = ./vendor/bin/phpunit
CSFIXER = ./vendor/bin/php-cs-fixer
STAN = ./vendor/bin/phpstan

.PHONY: run
run:
	$(COMPOSE) up -d $(DOCKER_HTTP)

build:
	$(COMPOSE) rm -vsf
	$(COMPOSE) down -v --remove-orphans
	$(COMPOSE) build $(DOCKER_APP)

pull:
	$(COMPOSE) pull	

dev:
	$(COMPOSE) run --no-deps $(DOCKER_APP) sh -c "$(COMPOSER) install --optimize-autoloader"

prod:
	$(COMPOSE) run --no-deps $(DOCKER_APP) sh -c "$(COMPOSER) install --no-dev --optimize-autoloader --classmap-authoritative"

down:
	$(COMPOSE) down --volumes

clean:
	$(COMPOSE) down --volumes --rmi=all
