<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PHPRestFramework;

use Symfony\Component\Yaml\Yaml;

class Rest {
    
    private $routes;
    
    /**
     * 
     * @param type $routefile
     * @throws Exception
     */
    public function __construct($routefile) {
        if(file_exists($routefile)){
            $this->routes = Yaml::parse(file_get_contents($routefile));
        }
        throw new Exception("Invalid route file");
    }
}