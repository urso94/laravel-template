.DEFAULT_GOAL := help

help: ## Display the help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Install the project
	cp -n .env.example .env || true
	cp -n code/.env.example code/.env || true
	docker compose run --rm --user dev fpm composer install
	docker compose run --rm --user dev fpm php artisan migrate

pint: ## Run laravel pint
	docker compose run --rm fpm ./vendor/bin/pint

phpstan: ## Run phpstan
	docker-compose run --rm fpm vendor/bin/phpstan analyse --memory-limit=256M
