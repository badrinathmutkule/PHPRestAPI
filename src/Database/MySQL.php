<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace MaskAPI\Database;

class MySQL {
    
    /**
     * Class instance
     * @var type 
     */
    public static $class_instance = null;
    
    /**
     * Private constructor
     */
    private function __construct() {
        //Private constructor;
    }
    
    /**
     * Get Instance 
     */
    public static function get_instance() {
        if(self::$class_instance == null){
            self::create_instance();
        }
        return self::$class_instance;
    }
    
    /**
     * CREATE INSTANCE
     * @throws \Exception
     */
    private static function create_instance() {
        try {
            $conn = new \PDO(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);            
            self::$class_instance = $conn;
        } catch (\PDOException $e) {
            throw new \Exception($e);
        }
        self::$class_instance = $conn;
    }
    
}