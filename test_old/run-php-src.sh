VERSION="master"
wget -q https://github.com/php/php-src/archive/$VERSION.tar.gz
mkdir -p ./data/php-src
tar -xzf ./$VERSION.tar.gz -C ./data/php-src --strip-components=1
php -n test_old/run.php --verbose --no-progress PHP7 ./data/php-src
