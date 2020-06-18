#!/bin/sh

cd $(dirname $0)/..

VER=$(head -n1 CHANGELOG.md  | cut -d' ' -f2)
echo "Get version from changelog: $VER"

php -d phar.readonly=0 \
vendor/bin/phpab \
  --phar \
  --all \
  --bzip2 \
  --output build/php-parse-$VER.phar \
  --template build/php-parse.tpl \
  lib

