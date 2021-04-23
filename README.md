[![Build Status](https://travis-ci.org/robopuff/tinypng.svg?branch=master)](https://travis-ci.org/robopuff/tinypng)

# TinyPNG API client for PHP

PHP client for the TinyPNG API, Read more at [official TinyPNG documentation](https://tinypng.com/developers/reference).

* [Documentation](https://robopuff.github.io/tinypng/)
* [Issues](https://github.com/robopuff/tinypng/issues)

To generate docs use [MKDocs](https://www.mkdocs.org/)

## Installation

```bash
composer require robopuff/tinypng
```

## Usage

```php
$tinyPng = new \TinyPng\TinyPng(new \TinyPng\Client\GuzzleClient('your_api_key'));
$tinyPng
    ->optimize(new \TinyPng\Input\Filesystem('unoptimized_image.png'))
    ->store(new \TinyPng\Output\Storage\Filesystem('optimized_image.png'));
```

## Running tests

```bash
composer test-suite
```

### Integration tests

```bash
TINYPNG_KEY=$YOUR_API_KEY composer test-integration
```

## License

This software is licensed under the BSD-3-Clause License. [View the license](LICENSE).
