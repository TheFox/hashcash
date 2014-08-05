
RM = rm -rf
PHPCS = vendor/bin/phpcs
PHPUNIT = vendor/bin/phpunit


all: install tests

install: composer.phar

update: composer.phar
	./composer.phar self-update
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
	$(RM) test_hashcashs*.yml

clean:
	$(RM) composer.lock composer.phar
	$(RM) vendor/*
	$(RM) vendor
