# PHP Rest API Framework

[![Build Status](https://travis-ci.org/slimphp/Slim.svg?branch=3.x)](https://travis-ci.org/slimphp/Slim)
[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim/badge.svg?branch=3.x)](https://coveralls.io/github/slimphp/Slim?branch=3.x)
[![Total Downloads](https://poser.pugx.org/slim/slim/downloads)](https://packagist.org/packages/slim/slim)
[![License](https://poser.pugx.org/slim/slim/license)](https://packagist.org/packages/slim/slim)

PHP Rest API is a PHP micro-framework that helps you quickly write simple yet powerful APIs.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install PHP Rest API.

```bash
$ composer require badrinathmutkule/phprestapi "^1.0"
```

This will install PHP Rest API and all required dependencies. PHP Rest API requires PHP 5.5.0 or newer.

## Usage

Create an index.php file with the following contents:

```php
<?php

require_once 'vendor/autoload.php';

$rest = new PHPRestFramework\Rest("routes.yaml");
$rest->serve();

```



You may quickly test this using the built-in PHP server:
```bash
$ php -S localhost:8000
```

Going to http://localhost:8000/hello/world will now display -

{
    "error": true,
    "code": 1000,
    "message": [
    "greetings": "Hello world"
    ],
    "process_time": 0.0000086408
}

For more information on how to configure your web server, see the [Documentation].

## Tests

To execute the test suite, you'll need phpunit.

```bash
$ phpunit
```

## Security

If you discover security related issues, please email security@slimframework.com instead of using the issue tracker.

## Credits

- [Badrinath Mutkule](https://github.com/badrinathmutkule)

## License

The PHP Rest API Framework is licensed under the MIT license. See [License File](LICENSE.md) for more information.
