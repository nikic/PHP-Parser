VERSION=$1
wget -q https://github.com/php/php-src/archive/php-$VERSION.tar.gz
mkdir -p ./data/php-src
tar -xzf ./php-$VERSION.tar.gz -C ./data/php-src --strip-components=1
php test_old/run.php --verbose --no-progress --php-version=$VERSION PHP ./data/php-src
