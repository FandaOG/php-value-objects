.PHONY: tests

COMPOSER_IMAGE=composer
PHP_IMAGE=php:7.4

composer:
	docker pull $(COMPOSER_IMAGE)
	docker run --rm --interactive --tty -w /app --volume `pwd`:/app $(COMPOSER_IMAGE) composer $(P)

composer_install:
	make composer P=install

composer_update:
	make composer P=update

phpunit:
	docker pull $(PHP_IMAGE)
	docker run --rm -it -w /app --volume `pwd`:/app $(PHP_IMAGE) ./vendor/bin/phpunit $(P)

tests:
	make phpunit P=tests
