# Usage

## Basic

```php
$tinyPng = new \TinyPng\TinyPng('api_key');

// Provide image path
$source = $tinyPng->fromFile('path_to_a_file.jpg');

// Provide image content
$source = $tinyPng->fromBuffer('file_content_as_string');

// Provide image via url
$source $tinyPng->fromUrl('http://example.com/image.png');

$source->toFile('output.file.png');
```

## Define client

Currently this library supports only `Guzzle` client, but you can specify options and client itself.
For more informations about Guzzle options, please read [official Guzzle documentation](http://docs.guzzlephp.org/)

```php
// You can specify GuzzleHttp client manually (no options will be applied)
// http://docs.guzzlephp.org/en/stable/request-options.html
$client = new \TinyPng\Client\GuzzleClient(['guzzle' => 'options'], null); 

// Client is a class that implements \TinyPng\Client\ClientInterface
$tinyPng = new \TinyPng\TinyPng('api_key', $client);
```

## Actions

### Resize image

```php
$tinyPng->fromFile('source.png')
  ->resize([
    'method' => 'fit',
    'width' => 50,
  ])
  ->toFile('output.png');
```

### Preserve metadata

```php
$tinyPng->fromFile('source.png')
  ->preserve(['copyright', 'creation', 'location'])
  ->toFile('output.png');
```

---

### Save to Amazon S3

```php
$tinyPng->fromFile('source.png')
  ->saveToAmazonS3([
    'aws_access_key_id' => '',
    'aws_secret_access_key' => '',
    'region' => '',
    'path' => 'bucket/path/filename'
  ]);
```

### Get optimised result

```
$image = $tinyPng->fromFile('source.png')->getImage();

$stream = $image->getDataStream(); // \Psr\Http\Message\StreamInterface
$metada = $image->getMetadata();   // \TinyPng\Image\Metadata
```

For more detailed explanation please refer to [official documentation](https://tinypng.com/developers/reference)
