set shell := ["cmd.exe", "/c"]
set quiet

COMPOSE := "docker compose"
DOCKER_HTTP := "httpd"
DOCKER_APP := "php"
CONTAINER_NAME := "ghibliql-php"
COMPOSER := "composer"

# Default recipe: show available commands
default:
    @just --list

[doc('Run containers (detached)')]
run:
	{{COMPOSE}} up -d {{DOCKER_HTTP}}

[doc('Build container')]
build:
	{{COMPOSE}} rm -vsf
	{{COMPOSE}} down -v --remove-orphans
	{{COMPOSE}} build {{DOCKER_APP}}

[doc('Pull containers')]
pull:
	{{COMPOSE}} pull --ignore-buildable

[doc('Composer: install packages (with dev)')]
dev:
	{{COMPOSE}} run {{DOCKER_APP}} sh -c "{{COMPOSER}} install --optimize-autoloader"

[doc('Composer: install packages (without dev)')]
prod:
	{{COMPOSE}} run {{DOCKER_APP}} sh -c "{{COMPOSER}} install --no-dev --optimize-autoloader --classmap-authoritative"

[doc('Generate a self-signed SSL certificate (for local use only)')]
ssl:
	openssl req -new -newkey rsa:4096 -days 365 -nodes -x509 -subj '/C=FR/ST=IDF/L=PAL/O=KOL/CN=localhost' -keyout ./docker/ssl/localhost.key -out ./docker/ssl/localhost.crt

[doc('Run a security audit')]
audit:
	gitleaks dir ./ --max-decode-depth 5 --redact=5 --verbose

[doc('Show live container logs')]
logs:
    {{COMPOSE}} logs -f {{DOCKER_APP}}

[doc('Open a shell to the container')]
shell:
    docker exec -it {{CONTAINER_NAME}} sh

[doc('Restart containers')]
restart:
    {{COMPOSE}} restart {{DOCKER_APP}}

[doc('Shutdown containers')]
stop:
	{{COMPOSE}} down --volumes

[doc('Shutdown & remove containers')]
clean:
	{{COMPOSE}} down --volumes --rmi=all
