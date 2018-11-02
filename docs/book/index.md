# Modern TinyPNG API for PHP7

PHP client for the TinyPNG API, Read more at [official TinyPNG documentation](https://tinypng.com/developers/reference).

```bash
$ composer require robopuff/tinypng
```

## Basic usage

```php
$tinyPng = new \TinyPng\TinyPng('your_api_key');
$tinyPng->fromFile('unoptimized_image.png')->toFile('optimized_image.png');
```

## License

This software is licensed under the BSD-3-Clause License. [View the license](https://github.com/robopuff/tinypng/blob/master/LICENSE).