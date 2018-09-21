wget -q https://github.com/php/php-src/archive/php-7.3.0RC1.tar.gz
mkdir -p ./data/php-src
tar -xzf ./php-7.3.0RC1.tar.gz -C ./data/php-src --strip-components=1
php -n test_old/run.php --verbose --no-progress PHP7 ./data/php-src
