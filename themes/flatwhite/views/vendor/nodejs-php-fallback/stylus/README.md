# stylus
[![Latest Stable Version](https://poser.pugx.org/nodejs-php-fallback/stylus/v/stable.png)](https://packagist.org/packages/nodejs-php-fallback/stylus)
[![Build Status](https://travis-ci.org/kylekatarnls/stylus.svg?branch=master)](https://travis-ci.org/kylekatarnls/stylus)
[![StyleCI](https://styleci.io/repos/63960936/shield?style=flat)](https://styleci.io/repos/63960936)
[![Test Coverage](https://codeclimate.com/github/kylekatarnls/stylus/badges/coverage.svg)](https://codecov.io/github/kylekatarnls/stylus?branch=master)
[![Code Climate](https://codeclimate.com/github/kylekatarnls/stylus/badges/gpa.svg)](https://codeclimate.com/github/kylekatarnls/stylus)

PHP wrapper to execute stylus node package or fallback to a PHP alternative.

## Usage

First you need [composer](https://getcomposer.org/) if you have not already. Then get the package with ```composer require nodejs-php-fallback/stylus``` then require the composer autload in your PHP file if it's not already:
```php
<?php

use NodejsPhpFallback\Stylus;

// Require the composer autload in your PHP file if it's not already.
// You do not need to if you use a framework with composer like Symfony, Laravel, etc.
require 'vendor/autoload.php';

$stylus = new Stylus('path/to/my-stylus-file.styl');

// Output to a file:
$stylus->write('path/to/my-css-file.css');

// Get CSS contents:
$cssContents = $stylus->getCss();

// Output to the browser:
header('Content-type: text/css');
echo $stylus->getCss();

// You can also get Stylus code from a string:
$stylus = new Stylus('
a
  color blue
  &:hover
    color navy
');
// Then write CSS with:
$stylus->write('path/to/my-css-file.css');
// or get it with:
$cssContents = $stylus->getCss();

// Pass true to the Stylus constructor to minify the rendered CSS:
$stylus = new Stylus('path/to/my-stylus-file.styl', true);
```
