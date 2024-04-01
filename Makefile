.PHONY: phpstan php-cs-fixer

tools/vendor:
	composer install -d tools

phpstan: tools/vendor
	tools/vendor/bin/phpstan

php-cs-fixer: tools/vendor
	tools/vendor/bin/php-cs-fixer fix
