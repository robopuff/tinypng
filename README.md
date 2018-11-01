[![Build Status](https://travis-ci.org/robopuff/tinypng.svg?branch=master)](https://travis-ci.org/robopuff/tinypng)

# TinyPNG API client for PHP

PHP client for the TinyPNG API, Read more at [official documentation](https://tinypng.com/developers/reference).

## Documentation

... to add

## Installation

```bash
composer require robopuff/tinypng
```

## Usage

```php
$tinyPng = new \TinyPng\TinyPng('your_api_key');
$tinyPng->fromFile('unoptimized_image.png')->toFile('optimized_image.png');
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

This software is licensed under the MIT License. [View the license](LICENSE).
