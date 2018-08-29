<?php

/**
 * The Input library for php rest api framework
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace PHPRestFramework;

class Input {

    /**
     * Constructor, don't allow to create instance of this class
     * so constructor is made private
     */
    private function __construct() {
        //Private consructor;
    }

    /**
     * Get URL Pattern for current request
     * @return string Returns Pattern
     */
    public static function pattern() {
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $request_url = explode("?", $url)[0];
        return $request_url;
    }

    /**
     * Get Request Method
     * @param type $upper
     * @return type
     */
    public static function method($upper = FALSE) {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get';
        return ($upper) ? strtoupper($method) : strtolower($method);
    }

    /**
     * Check if request is on HTTPS
     * @return type
     */
    public static function is_https() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
    }

    /**
     * Get User agent
     * @return string
     */
    public static function user_agent() {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "UNKNOWN";
        $user_agent = self::xss_clean($ua);
        return $user_agent != "" ? $user_agent : "UNKNOWN";
    }

    /**
     * Get IP address
     * @return string
     */
    public static function ip_address() {
        $server_params = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($server_params as $param) {
            if (isset($_SERVER[$param])) {
                return $_SERVER[$param];
            }
        }
        return '0.0.0.0';
    }

    /**
     * XSS Clean
     * @param string $data
     * @return string
     */
    public static function xss_clean($data) {
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }

    /**
     * Get raw json data
     * @return type
     */
    private static function get_body() {
        $string = file_get_contents('php://input');
        return (array) json_decode($string, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get query params
     * @param string $index
     */
    public static function get($index = FALSE) {
        if ($index === false) {
            return $_GET;
        }
        return array_key_exists($index, $_GET) ? $_GET[$index] : null;
    }

    /**
     * Get query params
     * @param string $index
     */
    public static function form($index = FALSE) {
        if ($index === false) {
            return $_POST;
        }
        return array_key_exists($index, $_POST) ? $_POST[$index] : null;
    }

    /**
     * Get Post Data
     * @param string $index
     * @return Mixed
     */
    public static function post($index = false) {
        $raw_data = self::get_body();
        if ($index === false) {
            return $raw_data;
        }
        return array_key_exists($index, $raw_data) ? $raw_data[$index] : null;
    }

    /**
     * Get Put Data
     * @return Mixed
     */
    public static function put($index = false) {
        $raw_data = self::get_body();
        if ($index === false) {
            return $raw_data;
        }
        return array_key_exists($index, $raw_data) ? $raw_data[$index] : null;
    }

    /**
     * Get parameters
     * @param string $index
     * @return Mixed
     */
    public static function delete($index = false) {
        $raw_data = self::get_body();
        if ($index === false) {
            return $raw_data;
        }
        return array_key_exists($index, $raw_data) ? $raw_data[$index] : null;
    }

    /**
     * get all input parameters including files and form data
     * @param string $index
     * @return Mixed
     */
    public static function param($index = false, $xss_filter = false) {
        
        $input = self::get_body();
        foreach ($_GET as $key => $val) {
            $input[$key] = $val;
        }
        foreach ($_POST as $key => $val) {
            $input[$key] = $val;
        }
        foreach ($_FILES as $key => $val) {
            $input[$key] = $val;
        }
        if ($index === false) {
            return $input;
        }
        
        if(array_key_exists($index, $input)) {
            return $xss_filter ? self::xss_clean($input[$index]) : $input[$index];
        } else {
            return null;
        }
        
    }

    /**
     * Get files 
     * @param string $index
     */
    public static function file($index = false) {
        if ($index === false) {
            return $_FILES;
        }
        return array_key_exists($index, $_FILES) ? $_FILES[$index] : null;
    }

    /**
     * Get Request Headers
     * @param string $index
     * @return type
     */
    public static function header($index = false) {
        $headers = self::get_all_headers();
        if ($index === false) {
            return $headers;
        }
        return array_key_exists(strtolower($index), $headers) ? $headers[strtolower($index)] : null;
    }

    /**
     * Fetch all request headers
     * @return type
     */
    private static function get_all_headers() {
        if (!function_exists('apache_request_headers')) {

            function apache_request_headers() {
                foreach ($_SERVER as $key => $value) {
                    if (substr($key, 0, 5) == "HTTP_") {
                        $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
                        $out[$key] = $value;
                    } else {
                        $out[$key] = $value;
                    }
                }
                return $out;
            }

        }
        $headers = [];
        foreach (apache_request_headers() as $key => $val) {
            $headers[strtolower($key)] = $val;
        }
        return $headers;
    }

}
