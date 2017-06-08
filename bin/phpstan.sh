#!/usr/bin/env bash

SCRIPT_BASEDIR=$(dirname "$0")


set -e
cd "${SCRIPT_BASEDIR}/.."

vendor/bin/phpstan analyse --no-progress --level 5 --configuration phpstan.neon --autoload-file vendor/autoload.php src tests
