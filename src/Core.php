<?php

/**
 * The Core Framework class
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace PHPRestFramework;

class Core {
    
    /**
     * Get Request Header/s
     * @param string $key optional
     * @return Mixed
     */
    public function _get_header($key = false){
        if($key !== false){
            return Input::header($key);
        }
        return Input::header();
    }
    
    /**
     * Get Query parameters sent through $_GET
     * @param string $key optional
     * @return Mixed
     */
    public function _get($key = false){
        if($key !== false){
            return Input::get($key);
        }
        return Input::get();
    }
    
    /**
     * Get POST parameters sent through body
     * @param string $key optional
     * @return Mixed
     */
    public function _post($key = false){
        if($key !== false){
            return Input::post($key);
        }
        return Input::post();
    }
    
    /**
     * Get PUT parameters sent through body
     * @param string $key optional
     * @return Mixed
     */
    public function _put($key = false){
        if($key !== false){
            return Input::put($key);
        }
        return Input::put();
    }
    
    /**
     * Get DELETE parameters sent through body
     * @param string $key optional
     * @return Mixed
     */
    public function _delete($key = false){
        if($key !== false){
            return Input::delete($key);
        }
        return Input::delete();
    }
    
    /**
     * Get FORM parameters sent through $_POST
     * @param string $key optional
     * @return Mixed
     */
    public function _form($key = false){
        if($key !== false){
            return Input::form($key);
        }
        return Input::form();
    }
    
    /**
     * Get FILES parameters sent through POST
     * @param string $key optional
     * @return Mixed
     */
    public function _file($key = false){
        if($key !== false){
            return Input::file($key);
        }
        return Input::file();
    }
    
    /**
     * Get Request Path
     * @return String
     */
    public function _get_path(){
        return Input::pattern();
    }
    
    
    /**
     * Check if request is through HTTPS
     * @return Boolean
     */
    public function _is_https(){
        return Input::isHTTPS();
    }
    
    
    /**
     * Get IP address
     * @return string
     */
    public function _get_ip_address(){
        return Input::ip_address();
    }
    
    /**
     * Get Request user_agent
     * @return String 
     */
    public function _get_user_agent(){
        return Input::user_agent();
    }
    
    
    /**
     * Get Method
     * @return string
     */
    public function _get_method(){
        return Input::method();
    }
    
    /**
     * Get filtered params sent through all methods
     * These params are available only after input validation
     * Not available inside auth methods
     * @param string $key optional
     * @return Mixed
     */
    public function _param($key = false, $xss_filter = false){
        if($key !== false){
            return Input::param($key, $xss_filter);
        }
        return Input::param();
    }
    
    
    /**
     * Set Request Context
     * @param string $key
     * @param Mixed $value
     */
    public function _set_context($key,$value){
        Context::set($key, $value);
    }
    
    
    /**
     * Set Context Array
     * @param array $context
     */
    public function _set_context_array(array $context){
        Context::setArray($context);
    }
    
    /**
     * Get Request Context
     * @param string $key Optional
     */
    public function _get_context($key = false){
        if($key === false){
            Context::get();
        }
        return Context::get($key);        
    }
    
    
    /**
     * Get Response Object
     * @param boolean $error
     * @param int $code
     * @param Array $message
     * @param array $data response data object
     * @return Array
     */
    public function _response_object($error, $code, $message, $data = array()){
        return response_object($error, $code, $message, $data);
    }
    
    /**
     * Returns unique key
     * @return string
     */
    public static function _unique_key() {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }
    
    
}