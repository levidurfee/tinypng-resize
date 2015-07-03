# tinypng-resize

[![Build Status](https://travis-ci.org/levidurfee/tinypng-resize.svg?branch=master)](https://travis-ci.org/levidurfee/tinypng-resize)
[![build v0.6.0](https://img.shields.io/badge/build-0.6.0-orange.svg)]()

> A service by [TinyPNG](https://tinypng.com). Get a free [API KEY](https://tinypng.com/developers).

**The primary purpose of this repo is to be used with another app that I am still developing. This repo at it's current state can be used by others for image resizing using TinyPNG.**

 If you have any issues or requests, please feel free to open an issue. Pull requests are welcome.

[TinyPNG](https://tinypng.com) has been very helpful. Even if you don't use my code, at least check out their site and the services they offer.

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

## quote from TinyPNG

> Images are resized by using the aspect ratio of the original image. Either the width or the height can be provided.

This may change.

## todo

- [ ] [#7](https://github.com/levidurfee/tinypng-resize/issues/7) when 1.0.0 is ready add it to packagist
- [ ] [#6](https://github.com/levidurfee/tinypng-resize/issues/6) write more tests
- [ ] [#8](https://github.com/levidurfee/tinypng-resize/issues/8) additional features for a later version
  - [ ] make resizing an option and not the main purpose of this code
  - [ ] [#5](https://github.com/levidurfee/tinypng-resize/issues/5) add in new S3 feature
  - [ ] [#4](https://github.com/levidurfee/tinypng-resize/issues/4) store previous unique reference urls so the original doesn't have to be uploaded each time
    - [ ] store the filename, md5 sum, and unique reference url of the image in a sqlite db
- [x] finish fopenShrink method for people who do not have curl
- [x] get tests working on travis-ci tests
- [x] add to packagist when done - 0.5.0 is added to packagist.

Using [RequestBin](http://requestb.in/) for some testing.
