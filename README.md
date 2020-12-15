# PHP Rest API Framework

[![Total Downloads](https://poser.pugx.org/salesmask/maskapi/downloads)](https://packagist.org/packages/salesmask/maskapi)
[![License](https://poser.pugx.org/salesmask/maskapi/license)](https://packagist.org/packages/salesmask/maskapi)

Mask API is a PHP micro-framework that helps you quickly write simple yet powerful restful APIs.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install PHP Rest API.

```bash
$ composer require salesmask/maskapi "dev-master"
```

This will install Mask API and all required dependencies. Mask API requires PHP 5.5.0 or newer.

## Usage

Create an index.php file with the following contents:

```php
<?php

require_once 'vendor/autoload.php';

$rest = new MaskAPI\Rest("routes.yaml");
$rest->serve();

```

Create routes.yaml file for defining your routes 

```yaml

- url: /hello/world
  method: get
  controller: User
  action: getGreetings
  auth: 
  validation: 

- url: /hello/world
  method: post
  controller: User
  action: postGreetings
  auth: 
  validation:
        message: required

```
Create a folder named Application where you will write all your models and controllers
Now create controller file named User.php under Application folder with following contents

```php
<?php

namespace Application;

class User extends \MaskAPI\Controller {

    public function __construct(){
        //write any construct code 
        //you can write pre validation here 
    }


    public function getGreetings(){
        //do stuff here 
        $code = 1000;
        $message = ["success"];
        $data = [
            "greetings" => "welcome to PHP Rest API Framework"
        ];
        return $this->_response_object(false, $code, $message, $data);
    }


    public function postGreetings(){

        //access input by using $this->_param("index");
        $message = $this->_param("message");

        //if you want to xss filter input use 
        $message = $this->_param("message", true);

        //do stuff with params
        //and return response 

        $code = 1000;
        $message = ["success"];

        return $this->_response_object(false, $code, $message);

        // $this->_response_object(); accepts 4 params as follows
        // 1 -> status boolean true/false
        // 2 -> status code (not http code ) any integer value
        // 3 -> array<string> message success ior error
        // 4 -> array<data> any data on success 

    }



}


```



You may quickly test this running the built-in PHP server:
```bash
$ php -S localhost:8000
```

Going to http://localhost:8000/hello/world will now display -

```json
{
    "error": true,
    "code": 1000,
    "message": ["success"],
    "data": {
        "greetings": "Welcome to PHP Rest API framework"
    },
    "process_time": 0.0000086408
}
```
For more information on how to configure your web server, see the [Documentation].


Available Validators
--------------------
* required `Ensures the specified key value exists and is not empty`
* valid_email `Checks for a valid email address`
* max_len,n `Checks key value length, makes sure it's not longer than the specified length. n = length parameter.`
* min_len,n `Checks key value length, makes sure it's not shorter than the specified length. n = length parameter.`
* exact_len,n `Ensures that the key value length precisely matches the specified length. n = length parameter.`
* alpha `Ensure only alpha characters are present in the key value (a-z, A-Z)`
* alpha_numeric `Ensure only alpha-numeric characters are present in the key value (a-z, A-Z, 0-9)`
* alpha_dash `Ensure only alpha-numeric characters + dashes and underscores are present in the key value (a-z, A-Z, 0-9, _-)`
* alpha_space `Ensure only alpha-numeric characters + spaces are present in the key value (a-z, A-Z, 0-9, \s)`
* numeric `Ensure only numeric key values`
* integer `Ensure only integer key values`
* boolean `Checks for PHP accepted boolean values, returns TRUE for "1", "true", "on" and "yes"`
* float `Checks for float values`
* valid_url `Check for valid URL or subdomain`
* url_exists `Check to see if the url exists and is accessible`
* valid_ip `Check for valid generic IP address`
* valid_ipv4 `Check for valid IPv4 address`
* valid_ipv6 `Check for valid IPv6 address`
* valid_cc `Check for a valid credit card number (Uses the MOD10 Checksum Algorithm)`
* valid_name `Check for a valid format human name`
* is_in,n `Verify that a value is contained within the pre-defined value set. The list of valid values must be provided in semicolon-separated list format (like so: value1;value2;value3;..;valuen). If a validation error occurs, the list of valid values is not revelead (this means, the error will just say the input is invalid, but it won't reveal the valid set to the user.`
* not_in,n `Verify that a value is not contained within the pre-defined value set. Semicolon (;) separated, list not outputted. See the rule above for more info.`
* street_address `Checks that the provided string is a likely street address. 1 number, 1 or more space, 1 or more letters`
* iban `Check for a valid IBAN`
* min_numeric `Determine if the provided numeric value is higher or equal to a specific value`
* max_numeric `Determine if the provided numeric value is lower or equal to a specific value`
* date `Determine if the provided input is a valid date (ISO 8601)`
* starts_with `Ensures the value starts with a certain character / set of character`
* phone_number `Validate phone numbers that match the following examples: 555-555-5555 , 5555425555, 555 555 5555, 1(519) 555-4444, 1 (519) 555-4422, 1-555-555-5555`
* regex `You can pass a custom regex using the following format: 'regex,/your-regex/'`
* valid_json `validate string to check if it's a valid json format`
* guidv4 `Determine if the provided string is a valid guidv4 format`
* valid_file `Determine if the value is a valid file uplopad`
* max_file_size `Determine if the uploaded file size is less than specified size in MB`
* allowed_extension `Determine if uploaded file extension is within the pre-defined value set valid values must be provided in semicolon-separated list format (like so: value1;value2;value3;..;valuen)`
* file_type `Determine if the uploaded file is of type within the pre-defined value set valid values must be provided in semicolon-separated list format (like so: value1;value2;value3;..;valuen)`
* valid_image `Determine if uploaded file is a valid image file`
* min_age `Determines if the age for given date is higher or equal to a specific value`
* password `Determines if the field is a valid password with eight characters including one uppercase letter, one special character and alphanumeric characters`
* optional `No validation is used for optional field`


## Generate swagger documentation for your apis 

create doc.php file with following content

```php

require_once 'vendor/autoload.php';

define('DEBUG_MODE', TRUE);

$swagger = new MaskAPI\Swagger();

$swagger->set_info([
    "version" => "1.0",
    "title" => "Test APIs",
    "description" => "Your api description goes here.",
    "license" => [
        "name" => "MIT",
        "url" => "http://github.com/gruntjs/grunt/blob/master/LICENSE-MIT"
    ]
]);

$swagger->set_host("localhost:8080");
$swagger->set_base_path("/");  
$swagger->set_schema("HTTP"); //htttp or https 
$swagger->set_route_file('routes.yaml'); //route file path
$swagger->generate('swagger.json');

```

run following command 

```bash
$ php doc.php
```

## Tests
To execute the test suite, you'll need phpunit.

```bash
$ phpunit
```

## Security

If you discover security related issues, please email badrinath@salesmask.com instead of using the issue tracker.

## Credits

- [Badrinath M](https://github.com/salesmask)

## License

The Mask API Framework is licensed under the MIT license. See [License File](LICENSE.md) for more information.
