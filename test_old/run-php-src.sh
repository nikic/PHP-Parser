VERSION=$1
if [[ ! -f php-$VERSION.tar.gz ]]; then
    wget -q https://github.com/php/php-src/archive/php-$VERSION.tar.gz
fi
rm -rf ./data/php-src
mkdir -p ./data/php-src
tar -xzf ./php-$VERSION.tar.gz -C ./data/php-src --strip-components=1
php test_old/run.php --verbose --no-progress --php-version=$VERSION PHP ./data/php-src
