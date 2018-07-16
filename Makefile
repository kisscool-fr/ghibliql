# -*- mode: makefile -*-

COMPOSE = docker-compose
PHPUNIT = ./vendor/bin/phpunit

.PHONY: run
run: 
	$(COMPOSE) up web

update:
	$(COMPOSE) pull
	$(COMPOSE) up composer

test:
	$(PHPUNIT) --bootstrap vendor/autoload.php --testdox tests

down:
	$(COMPOSE) down --volumes --rmi=local
