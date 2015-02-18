
RM = rm -rf
CHMOD = chmod
MKDIR = mkdir -p
VENDOR = vendor
PHPCS = vendor/bin/phpcs
PHPCS_STANDARD = vendor/thefox/phpcsrs/Standards/TheFox
PHPCS_REPORT = --report=full --report-width=160
PHPUNIT = vendor/bin/phpunit
COMPOSER = ./composer.phar
COMPOSER_DEV ?= --dev


.PHONY: all install update test test_phpcs test_phpunit test_phpunit_cc test_clean clean

all: install test

install: $(VENDOR)

update: $(COMPOSER)
	$(COMPOSER) selfupdate
	$(COMPOSER) update

test: test_phpcs test_phpunit

test_phpcs: $(PHPCS) vendor/thefox/phpcsrs/Standards/TheFox
	$(PHPCS) -v -s $(PHPCS_REPORT) --standard=$(PHPCS_STANDARD) src tests *.php

test_phpunit: $(PHPUNIT) phpunit.xml test_data
	TEST=true $(PHPUNIT) $(PHPUNIT_COVERAGE_HTML) $(PHPUNIT_COVERAGE_CLOVER)
	$(MAKE) test_clean

test_phpunit_cc: build
	$(MAKE) test_phpunit PHPUNIT_COVERAGE_HTML="--coverage-html build/report"

test_clean:
	$(RM) test_data

clean:
	$(RM) composer.lock $(COMPOSER)
	$(RM) vendor/*
	$(RM) vendor

$(VENDOR): $(COMPOSER)
	$(COMPOSER) install $(COMPOSER_PREFER_SOURCE) --no-interaction $(COMPOSER_DEV)

$(COMPOSER):
	curl -sS https://getcomposer.org/installer | php
	$(CHMOD) a+rx-w,u+w $(COMPOSER)

$(PHPCS): $(VENDOR)

$(PHPUNIT): $(VENDOR)

test_data:
	$(MKDIR) test_data

build:
	$(MKDIR) build
	$(MKDIR) build/logs
	$(CHMOD) a-rwx,u+rwx build
