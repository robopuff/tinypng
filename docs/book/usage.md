# Usage

## Basic

```php
$tinyPng = new \TinyPng\TinyPng(new \TinyPng\Client\GuzzleClient('your_api_key'));

// Provide image path
$output = $tinyPng->optimize(new \TinyPng\Input\Filesystem('unoptimized_image.png'));

// Provide image by url
$output = $tinyPng->optimize(new \TinyPng\Input\Guzzle('http://example.com/image.png'));

// Save image 
$output->store(new \TinyPng\Output\Storage\Filesystem('output.file.png'));
```

## Define a client

Currently, this library supports only `Guzzle` client, but you can specify options and client itself.
For more information about Guzzle options, please read [official Guzzle documentation](http://docs.guzzlephp.org/)

```php
// You can specify GuzzleHttp client manually (no options will be applied)
// http://docs.guzzlephp.org/en/stable/request-options.html
$client = new \TinyPng\Client\GuzzleClient('api_key', ['guzzle' => 'options'], new \GuzzleHttp\Client());
```

## Actions

### Resize image

```php
$tinyPng = new \TinyPng\TinyPng(new \TinyPng\Client\GuzzleClient('your_api_key'));
$output = $tinyPng->optimize(new \TinyPng\Input\Filesystem('unoptimized_image.png'));

$output->setCommands(
    new \TinyPng\Output\Command\Resize(
        \TinyPng\Output\Command\Resize::METHOD_FIT,
        50
    )
);
$output->store(new \TinyPng\Output\Storage\Filesystem('output.png'));
```

### Preserve metadata

```php
$tinyPng = new \TinyPng\TinyPng(new \TinyPng\Client\GuzzleClient('your_api_key'));
$output = $tinyPng->optimize(new \TinyPng\Input\Filesystem('unoptimized_image.png'));

$output->setCommands(
    new \TinyPng\Output\Command\Metadata(
        \TinyPng\Output\Command\Metadata::METADATA_COPYRIGHT,
        \TinyPng\Output\Command\Metadata::METADATA_CREATION,
        \TinyPng\Output\Command\Metadata::METADATA_LOCATION,
    )
);
$output->store(new \TinyPng\Output\Storage\Filesystem('output.png'));
```

---

### Save to Amazon S3

```php
$tinyPng = new \TinyPng\TinyPng(new \TinyPng\Client\GuzzleClient('your_api_key'));
$output = $tinyPng->optimize(new \TinyPng\Input\Filesystem('unoptimized_image.png'));
$output->store(new \TinyPng\Output\Storage\AmazonS3([
    'aws_access_key_id' => '',
    'aws_secret_access_key' => '',
    'region' => '',
    'path' => 'bucket/path/filename'
]));
```

For more detailed explanation please refer to [official documentation](https://tinypng.com/developers/reference)
