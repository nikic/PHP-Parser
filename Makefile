.PHONY: phpstan php-cs-fixer

tools/vendor:
	composer install -d tools

phpstan: tools/vendor
	php tools/vendor/bin/phpstan

php-cs-fixer: tools/vendor
	php tools/vendor/bin/php-cs-fixer fix

tests:
	php vendor/bin/phpunit