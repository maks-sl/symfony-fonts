up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up
console: cli-bash
test: app-test

perm:
	sudo chown -R ${USER}:$$(id -gn ${USER}) ./app

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

cli-bash:
	docker-compose run --rm php-cli bash

app-test:
	docker-compose run --rm php-cli php bin/phpunit