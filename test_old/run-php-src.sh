if [[ $1 == '7' ]]; then
	VERSION='PHP-7.4'
else
	VERSION='master'
fi
wget -q https://github.com/php/php-src/archive/$VERSION.tar.gz
mkdir -p ./data/php-src
tar -xzf ./$VERSION.tar.gz -C ./data/php-src --strip-components=1
php -n test_old/run.php --verbose --no-progress PHP$1 ./data/php-src
