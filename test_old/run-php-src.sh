if [[ $1 == '7' ]]; then
	VERSION='fa9bd812fcab6dfd6d9b506cb3cb04dfa75d239d'
else
	VERSION='05478e985eb50c473054b4f1bf174f48ead78784'
fi
wget -q https://github.com/php/php-src/archive/$VERSION.tar.gz
mkdir -p ./data/php-src
tar -xzf ./$VERSION.tar.gz -C ./data/php-src --strip-components=1
php -n test_old/run.php --verbose --no-progress PHP$1 ./data/php-src
