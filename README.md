# tinypng-resize

[![Build Status](https://travis-ci.org/levidurfee/tinypng-resize.svg?branch=master)](https://travis-ci.org/levidurfee/tinypng-resize)
[![build v0.5.0](https://img.shields.io/badge/build-0.5.0-orange.svg)]()

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

To set the image max width or height to 150:

```php
require_once('vendor/autoload.php');
$tp = new teklife\Tinypng('YOUR_API_KEY');
$tp->shrink('ignore/helicopter-original.png', 'ignore/helicopter-new.png', 150, 150, true);
```

*The image will either be 150px wide or 150px tall. If it is 150px wide, then the height will be smaller than 150px. If it is 150px tall, then the width will be smaller than 150px. It bases this off the original image size. It will not crop the image.*

> Images are resized by using the aspect ratio of the original image. Either the width or the height can be provided.

# todo

- [x] finish fopenShrink method for people who do not have curl
- [ ] write more tests
- [x] get tests working on travis-ci tests
- [ ] add to packagist when done - if that is okay with TinyPNG :]

Using [RequestBin](http://requestb.in/) for testing.
