<?php

/**
 * The Context class
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace MaskAPI\Core;

class Context {
    
    /**
     * Store request context
     * @var array 
     */
    private static $context = [];
    /**
     * Private Constructor
     */
    private function __construct() {
        //Private Constructor;
    }    
    /**
     * Set Context
     * @param string $key
     * @param Mixed $value
     */
    public static function set($key,$value){
        self::$context[$key] = $value;
    }    
    
    /**
     * Set context array
     * @param array $contextArray
     */
    public static function setArray(array $contextArray){
        foreach ($contextArray as $key => $value){
            self::$context[$key] = $value;
        }
    }
    
    /**
     * Get context
     * @param string $key
     * @return Mixed
     */
    public static function get($key = false){
        if($key !== false){
            return isset(self::$context[$key]) ? self::$context[$key] : false;
        }
        return self::$context;
    }
    
}