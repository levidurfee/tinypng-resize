# tinypng-resize

[![Build Status](https://travis-ci.org/levidurfee/tinypng-resize.svg?branch=master)](https://travis-ci.org/levidurfee/tinypng-resize)
[![build v0.1.0](https://img.shields.io/badge/build-0.1.0-orange.svg)]()

> A service by [tinypng](https://tinypng.com).

## example usage

```php
<?php
require_once('vendor/autoload.php');
$tp = new teklife\Tinypng('YOUR_API_KEY');
$tp->shrink('ignore/helicopter-original.png', 'ignore/helicopter-new.png', 150);
```
