# PHP Rest API Framework

[![Total Downloads](https://poser.pugx.org/salesmask/maskapi/downloads)](https://packagist.org/packages/salesmask/maskapi)
[![License](https://poser.pugx.org/salesmask/maskapi/license)](https://packagist.org/packages/salesmask/maskapi)

MaskAPI is a PHP micro-framework that helps you quickly write simple yet powerful restful APIs.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install MaskAPI.

```bash
$ composer require salesmask/maskapi
```

This will install MaskAPI and all required dependencies. MaskAPI requires PHP 5.5.0 or newer.

## Usage
Create a tool.php file with following contents:

```php
<?php

require_once 'vendor/autoload.php';

$tool = new MaskAPI\Devtool();
$tool->run(__DIR__);

```
Run following command to generate config file:
```bash
$ php tool.php init
```
Above command should generate config.development.php file in project root folder 
Edit configurations and include config file in tool.php as shown here:

```php
<?php

require_once 'vendor/autoload.php';
require_once 'config.development.php';

$tool = new MaskAPI\Devtool();
$tool->run(__DIR__);

```
Note: PUBLIC_DIRECTORY constant defination in the configuration file it is set to www you can change it as per your preferance:

```php
/**
 * Public directory
 */
define("PUBLIC_DIRECTORY", "www");

```

Now run following command to generate public directory and api documentation:

```bash
$ php tool.php doc
```
Above command should generate www folder (public directory) in your project root directory and add doc folder under it with SwaggerUI codebase


Create an index.php file inside www folder with the following contents:

```php
<?php

require_once '../vendor/autoload.php';

$app = new MaskAPI\Application("../routes.yaml");
$app->serve();

```

Create routes.yaml file inside project root directory for defining your API routes:

```yaml

- url: /hello/world
  method: get
  controller: MyController
  action: sayHello
  auth: 
  validation: 

- url: /hello/user
  method: post
  controller: MyController
  action: showHello
  auth: 
  validation:
        data: required

```
Create a folder named Application and under it create controller file named MyController.php with following contents:

```php
<?php

namespace Application;

class MyController extends \MaskAPI\Controller {

    public function __construct(){
        //write any construct code 
        //you can write pre validation here 
    }


    public function sayHello(){
        //do stuff here 
        $code = 1000;
        $message = ["success"];
        $data = [
            "hello" => "welcome to MaskAPI"
        ];
        return $this->_response_object(false, $code, $message, $data);
    }


    public function showHello(){

        //access input by using $this->_param("index");
        $data = $this->_param("data");

        //if you want to xss filter input use 
        //$data = $this->_param("data", true);

        //do stuff and return response 

        $code = 1000;
        $message = ["success"];
        $result = [$data];

        return $this->_response_object(false, $code, $message, $result);
        
    }


}


```



You may quickly test this running the built-in PHP server inside www directory:
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
        "hello": "Welcome to MaskAPI"
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

The MaskAPI is licensed under the MIT license. See [License File](LICENSE.md) for more information.
