help: ## Shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

init: ## Install composer dependencies
	composer install

update: ## Update composer dependencies
	composer upgrade

cs-fix: ## Run php-cs-fixer
	php vendor/bin/php-cs-fixer fix

deptrac: ## Run deptrac
	php vendor/bin/deptrac --fail-on-uncovered --formatter-console-report-uncovered=true

phpstan: ## Run phpstan
	php vendor/bin/phpstan analyze

psalm: ## Run Psalm
	php vendor/bin/psalm.phar --threads=8

tests: cs-fix deptrac phpstan psalm ## Run all tests
