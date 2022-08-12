init: docker-build \
 docker-up \
 api-init \
 frontend-init \
 docker-down

api-init: composer-install
api-update: composer-update

start: frontend-clear docker-up api-migrate api-fixtures frontend-init
#start: docker-up api-migrate api-fixtures
stop: docker-down

frontend-init: frontend-yarn-install frontend-ready
frontend-update: frontend-clear frontend-yarn-update

frontend-yarn-install:
	docker-compose run --rm frontend-node-cli yarn install

frontend-yarn-update:
	docker-compose run --rm frontend-node-cli yarn upgrade

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready build'

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

docker-build:
	docker-compose down
	docker-compose build

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down

composer-install:
	docker-compose run --rm php-cli composer install

composer-update:
	docker-compose run --rm php-cli composer update

api-validate:
	docker-compose run --rm php-cli php console.php orm:validate-schema

api-migrate:
	docker-compose run --rm php-cli php console.php migrate --no-interaction

api-fixtures:
	docker-compose run --rm php-cli php console.php fixture

unit-test:
	docker-compose run --rm php-cli composer test

phpcs:
	docker-compose run --rm php-cli composer phpcs

psalm-check:
	docker-compose run --rm php-cli composer psalm

code-check: unit-test phpcs psalm-check api-validate
