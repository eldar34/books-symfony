.DEFAULT_GOAL := help

# Current user ID and group ID except MacOS where it conflicts with Docker abilities
ifeq ($(shell uname), Darwin)
    export UID=1000
    export GID=1000
else
    export UID=$(shell id -u)
    export GID=$(shell id -g)
endif

# Включаем файл .env из директории ./docker
# include ./docker/.env
# export $(shell sed '/^#/d; s/=.*//' ./docker/.env)

DOCKER_COMPOSE_DEV := docker compose

init: build composer-install asset-compile

build: ## Build docker image
	$(DOCKER_COMPOSE_DEV) build

up: ## Up the dev environment
	$(DOCKER_COMPOSE_DEV) up -d

down: ## Down the dev environment
	$(DOCKER_COMPOSE_DEV) down --remove-orphans

clear: ## Remove development docker containers and volumes
	$(DOCKER_COMPOSE_DEV) down --volumes --remove-orphans

shell: ## Get into container shell
	$(DOCKER_COMPOSE_DEV) exec -u $(UID):$(GID) app-php bash

composer-install: ## Run composer install
	$(DOCKER_COMPOSE_DEV) run --rm app-php composer install

composer-update: ## Run composer update
	$(DOCKER_COMPOSE_DEV) run --rm app-php composer update

cache-clear: ## Clear cache
	$(DOCKER_COMPOSE_DEV) run --rm app-php php bin/console cache:clear

asset-compile: ## Compile assets via AssetMapper (for production/warmup)
	$(DOCKER_COMPOSE_DEV) run --rm app-php php bin/console asset-map:compile

migrate-up: ## Run migrations
	$(DOCKER_COMPOSE_DEV) run --rm app-php php bin/console doctrine:migrations:migrate --no-interaction

migrate-down: ## Rollback last migration
	$(DOCKER_COMPOSE_DEV) run --rm app-php php bin/console doctrine:migrations:migrate prev --no-interaction

fixture-load: ## Run load fixtures
	$(DOCKER_COMPOSE_DEV) run --rm app-php php bin/console doctrine:fixtures:load --no-interaction

queue-work: ## Run queue
	$(DOCKER_COMPOSE_DEV) run --rm app-php php bin/console messenger:consume async -v



# Output the help for each task, see https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
