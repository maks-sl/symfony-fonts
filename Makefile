up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up app-init-auto
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

app-init-auto: app-composer-install app-wait-db app-migrations app-fixtures

app-composer-install:
	docker-compose run --rm php-cli composer install

app-wait-db:
	until docker-compose exec -T postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

app-migrations:
	docker-compose run --rm php-cli php bin/console do:mi:mi --no-interaction

app-fixtures:
	docker-compose run --rm php-cli php bin/console do:fi:lo --no-interaction