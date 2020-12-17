<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MaskAPI\Helper;

class Response {

    private static $response_headers = [
        "Access-Control-Allow-Origin" => "*",
        "Access-Control-Allow-Headers" => "Origin, Content-Type, ApiKey, AuthToken, AccessToken, RefreshToken",
        "Content-Type" => "application/json; charset=UTF-8",
        "x-content-type-options" => "nosnif",
        "x-xss-protection" => "1; mode=block",
        "x-frame-options" => "sameorigin",
        "cache-control" => "no-cache, no-store, max-age=0, must-revalidate"
    ];
    
    /**
     * Constructor, don't allow to create instance of this class
     * so constructor is made private
     */
    private function __construct() {
        //Private consructor;
    }
    
    
    public static function set_response_header($key, $value){
        self::$response_headers[$key] = $value;
    }

    /**
     * Response object
     * @param boolean $error true or false value for error
     * @param int $code an error or success code
     * @param array $message 
     * @param array $data optional
     * return final array
     */
    public static function response_object($error, $code, array $message, $data = array()) {

        $finalData = [
            'error' => $error,
            'code' => $code,
            'message' => $message
        ];

        if (!empty($data)) {
            $finalData['data'] = $data;
        }
        return $finalData;
    }

    /**
     * response 
     * @param type $status
     * @param array $data
     */
    public static function response($status, array $data) {
        
        foreach(self::$response_headers as $key => $val){
            header($key . ": ". $val);
        }
        
        http_response_code($status);
        $data['process_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        echo json_encode($data);
        
        exit;
    }

}
