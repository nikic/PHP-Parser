#!/bin/sh

set -e
cd $(dirname $0)/..
set -x

: Get version from changelog
VER=$(head -n1 CHANGELOG.md  | cut -d' ' -f2)

: Create phar stub
php -r '
	$cmd = file_get_contents("bin/php-parse");
	$pc = strpos($cmd, "// END AUTOLOADER");
	$aut = file_get_contents("vendor/theseer/autoload/src/templates/ci/phar.php.tpl");
	$pa = strpos($aut, "__HALT_COMPILER");
	if ($cmd && $pc && $aut && $pa) {
		file_put_contents("build/php-parse.tpl", substr($aut, 0, $pa) .	substr($cmd, $pc) .	substr($aut, $pa));
	} else {
		exit (1);
	}
'
: Create phar for the command
php -d phar.readonly=0 \
vendor/bin/phpab \
  --phar \
  --all \
  --output build/php-parse-cmd-$VER.phar \
  --template build/php-parse.tpl \
  lib

: Create phar for the library
php -d phar.readonly=0 \
vendor/bin/phpab \
  --phar \
  --all \
  --output build/php-parse-lib-$VER.phar \
  lib

