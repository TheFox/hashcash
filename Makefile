
DELETE = rm -rf
PHPCS = vendor/bin/phpcs
PHPUNIT = vendor/bin/phpunit


default: install tests

install: composer.phar

update: composer.phar
	./composer.phar update

composer.phar:
	curl -sS https://getcomposer.org/installer | php
	./composer.phar install

$(PHPCS): composer.phar

tests: test_phpcs test_phpunit

test_phpcs: $(PHPCS) vendor/thefox/phpcsrs/Standards/TheFox
	$(PHPCS) -v -s --report=full --report-width=160 --standard=vendor/thefox/phpcsrs/Standards/TheFox src tests

test_phpunit: $(PHPUNIT) phpunit.xml
	$(PHPUNIT)

clean:
	$(DELETE) composer.lock composer.phar
	$(DELETE) vendor/*
	$(DELETE) vendor
