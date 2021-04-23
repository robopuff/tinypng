# Modern TinyPNG API for PHP7

PHP client for the TinyPNG API, Read more at [official TinyPNG documentation](https://tinypng.com/developers/reference).

```bash
$ composer require robopuff/tinypng
```

## Basic usage

```php
$tinyPng = new \TinyPng\TinyPng(new \TinyPng\Client\GuzzleClient('your_api_key'));
$tinyPng
    ->optimize(new \TinyPng\Input\Filesystem('unoptimized_image.png'))
    ->store(new \TinyPng\Output\Storage\Filesystem('optimized_image.png'));
```

## License

This software is licensed under the BSD-3-Clause License. [View the license](https://github.com/robopuff/tinypng/blob/master/LICENSE).
