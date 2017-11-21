<?php

use Symfony\Component\Yaml\Yaml;

require_once 'vendor/autoload.php';

$value = Yaml::parse(file_get_contents('route.yaml'));
var_dump($value);