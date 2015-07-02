# tinypng-resize

[![Build Status](https://travis-ci.org/levidurfee/tinypng-resize.svg?branch=master)](https://travis-ci.org/levidurfee/tinypng-resize)
[![build v0.1.0](https://img.shields.io/badge/build-0.1.0-orange.svg)]()

> A service by [tinypng](https://tinypng.com).

## example usage

To resize based on width:

```php
<?php
require_once('vendor/autoload.php');
$tp = new teklife\Tinypng('YOUR_API_KEY');
$tp->shrink('ignore/helicopter-original.png', 'ignore/helicopter-new.png', 150);
```
To resize based on height:

```php
<?php
require_once('vendor/autoload.php');
$tp = new teklife\Tinypng('YOUR_API_KEY');
$tp->shrink('ignore/helicopter-original.png', 'ignore/helicopter-new.png', '', 150);
```
> Images are resized by using the aspect ratio of the original image. Either the width or the height can be provided.

# todo

* finish fopenShrink method for people who do not have curl
* write tests - not sure how to do this yet, without publishing my API key
* add to packagist when done - if that is okay with TinyPNG :]
