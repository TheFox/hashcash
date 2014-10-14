
RM = rm -rf
CHMOD = chmod
PHPCS = vendor/bin/phpcs
PHPUNIT = vendor/bin/phpunit


.PHONY: all install update tests test_phpcs test_phpunit clean

all: install tests

install: composer.phar

update: composer.phar
	./composer.phar selfupdate
	./composer.phar update

composer.phar:
	curl -sS https://getcomposer.org/installer | php
	$(CHMOD) 755 ./composer.phar
	./composer.phar install

$(PHPCS): composer.phar

tests: test_phpcs test_phpunit

test_phpcs: $(PHPCS) vendor/thefox/phpcsrs/Standards/TheFox
	$(PHPCS) -v -s --report=full --report-width=160 --standard=vendor/thefox/phpcsrs/Standards/TheFox src tests *.php

test_phpunit: $(PHPUNIT) phpunit.xml
	$(PHPUNIT)
	$(RM) test_hashcashs*.yml

clean:
	$(RM) composer.lock composer.phar
	$(RM) vendor/*
	$(RM) vendor
