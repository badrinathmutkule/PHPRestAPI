<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MaskAPI;
use MaskAPI\Core\Rest;

class Application {
    
    
    private $rest;
    /**
     * Constructor
     * @param type $routeFile
     */
    public function __construct($routeFile) {
        //consructor;
        $this->rest = new Rest($routeFile);
    }
    
    
    /**
     * Serve the application
     */
    public function serve(){
        $this->rest->serve();
    }
    
}
