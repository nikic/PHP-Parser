Installation
============

There are multiple ways to include the PHP parser into your project:

Installing via Composer
-----------------------

Run the following command inside your project:

    php composer.phar require nikic/php-parser

If you haven't installed [Composer][1] yet, you can do so using:

    curl -s http://getcomposer.org/installer | php

Installing as a Git Submodule
-----------------------------

Run the following command to install the parser into the `vendor/PHP-Parser` folder:

    git submodule add git://github.com/nikic/PHP-Parser.git vendor/PHP-Parser

Installing from the Zip- or Tarball
-----------------------------------

Download the latest version from [the download page][2], unpack it and move the files somewhere into your project.


 [1]: https://getcomposer.org/
 [2]: https://github.com/nikic/PHP-Parser/tags
