<?php

/**
 * The validation library for input validation
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace PHPRestFramework;

class Validation {

    /**
     * List of errors
     * @var array 
     */
    protected $errors = array();

    /**
     * run validation for given input
     * @param array $input
     * @param array $validations
     * @throws \Exception
     */
    public function run(array $input, array $validations) {
        foreach ($validations as $field => $validators) {
            $value = isset($input[$field]) ? $input[$field] : null;
            $error = $this->validate_field($field, $validators, $value);
            if ($error !== false) {
                $this->errors[] = $error;
            }
        }
        return $this->errors;

        
    }

    /**
     * Validate individual input parameter
     * @param string $field
     * @param string $validators
     * @param Mixed $value
     * @throws \Exception
     */
    private function validate_field($field, $validators, $value) {
        $validator_array = explode('|', $validators);
        
        foreach ($validator_array as $validation) {
            $validation = explode(",", $validation);
            $method = $validation[0];
            $param = isset($validation[1]) ? $validation[1] : "";
            $validation_method = "validate_" . $method;
            
            if (method_exists($this, $validation_method)) {
                $error = $this->{$validation_method}($field, $value, $param);
                if($error !== false){
                    return $error;
                }
            } else {
                throw new \Exception("Invalid validation method [$method]");
            }
        }
        
        return false;
    }

    /**
     * Validate if value is in given list 
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_is_in($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        $param = trim(strtolower($param));
        $value = trim(strtolower($input));
        $param_array = explode(';', $param);
        if (in_array($value, $param_array)) {
            return false;
        }
        return "The [$field] field needs to contain a value from: [$param]";
    }

    /**
     * Validate if value is not in given list 
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_not_in($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        $param = trim(strtolower($param));
        $value = trim(strtolower($input));
        $param_array = explode(';', $param);
        if (!in_array($value, $param_array)) {
            return false;
        }
        return "The [$field] field contains a value that is not accepted";
    }

    /**
     * Check if field is present and is not empty
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_required($field, $input, $param = null) {
        if ($input === false || $input === 0 || $input === 0.0 || $input === '0' || !empty($input)) {
            return false;
        }
        return "The [$field] field is required";
    }

    /**
     * Determine if the value is valid email
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_email($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return "The [$field] field is required to be a valid email address";
        }
        return false;
    }

    /**
     * Validate the maximum length of value
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_max_len($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input) <= (int) $param) {
                return false;
            }
        } else {
            if (strlen($input) <= (int) $param) {
                return false;
            }
        }
        return "The [$field] field needs to be [$param] or shorter in length";
    }

    /**
     * Validate the minium length of value
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_min_len($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input) >= (int) $param) {
                return false;
            }
        } else {
            if (strlen($input) >= (int) $param) {
                return false;
            }
        }
        return "The [$field] field needs to be [$param] or longer in length";
    }

    /**
     * Validate the length of value is equal to given param
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_exact_len($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input) == (int) $param) {
                return false;
            }
        } else {
            if (strlen($input) == (int) $param) {
                return false;
            }
        }
        return "The [$field] field needs to be exactly [$param] characters in length";
    }

    /**
     * Validate if value is alphabet only
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_alpha($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $input) !== false) {
            return "The [$field] field may only contain alpha characters(a-z)";
        }
        return false;
    }

    /**
     * Validate if value is alpha numeric
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_alpha_numeric($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $input) !== false) {
            return "The [$field] field may only contain alpha-numeric characters";
        }
        return false;
    }

    /**
     * Validate if value is alpha+dash
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_alpha_dash($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $input) !== false) {
            return "The [$field] field may only contain alpha characters and dashes";
        }
        return false;
    }

    /**
     * Validate if value is alpha+space
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_alpha_space($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i", $input) !== false) {
            return "The [$field] field may only contain alpha characters and spaces";
        }
        return false;
    }

    /**
     * Validate if value is numeric
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_numeric($field, $input, $param = null) {
        if($input === null){
            return false;
        }

        if (!is_numeric($input)) {
            return "The [$field] field may only contain numeric characters";
        }
        return false;
    }

    /**
     * Validate if value is integer
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_integer($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (filter_var($input, FILTER_VALIDATE_INT) === false) {
            return "The [$field] field may only contain a numeric value";
        }
        return false;
    }

    /**
     * Validate if value is boolean
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_boolean($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if ($input === true || $input === false) {
            return false;
        }
        return "The [$field] field may only contain a true or false value";
    }

    /**
     * Validate if value is float
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_float($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (filter_var($input, FILTER_VALIDATE_FLOAT) === false) {
            return "The [$field] field may only contain a float value";
        }
        return false;
    }

    /**
     * Validate if the string is valid url
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_url($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!filter_var($input, FILTER_VALIDATE_URL)) {
            return "The [$field] field is required to be a valid URL";
        }
        return false;
    }

    /**
     * Validate if the string is valid url and url exiats
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_url_exists($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        $url = parse_url(strtolower($input));
        if (isset($url['host'])) {
            $url = $url['host'];
        }
        if (function_exists('checkdnsrr')) {
            if (checkdnsrr($url) === false) {
                return "The [$field] URL does not exist";
            }
        } else {
            if (gethostbyname($url) == $url) {
                return "The [$field] URL does not exist";
            }
        }
        return false;
    }

    /**
     * Validate if the string is valid IP address
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_ip($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!filter_var($input, FILTER_VALIDATE_IP) !== false) {
            return "The [$field] field needs to contain a valid IP address";
        }
        return false;
    }

    /**
     * Validate if the string is valid IPV4
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_ipv4($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return "The [$field] field needs to contain a valid IPV4 address";
        }
        return false;
    }

    /**
     * Validate if the string is valid IPV6
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_ipv6($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return "The [$field] field needs to contain a valid IPV6 address";
        }
        return false;
    }

    /**
     * Validate if the string is valid credit card number
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_cc($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        $number = preg_replace('/\D/', '', $input);
        if (function_exists('mb_strlen')) {
            $number_length = mb_strlen($number);
        } else {
            $number_length = strlen($number);
        }
        $parity = $number_length % 2;
        $total = 0;
        for ($i = 0; $i < $number_length; ++$i) {
            $digit = $number[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $total += $digit;
        }
        if ($total % 10 == 0) {
            return false;
        }
        return "The [$field] field needs to contain a valid credit card number";
    }

    /**
     * Validate if the string is valid human name
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_name($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (!preg_match("/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïñðòóôõöùúûüýÿ '-])+$/i", $input) !== false) {
            return "The [$field] field needs to contain a valid human name";
        }
        return false;
    }

    /**
     * Validate if the string is valid street address
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_street_address($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        // Theory: 1 number, 1 or more spaces, 1 or more words
        $hasLetter = preg_match('/[a-zA-Z]/', $input);
        $hasDigit = preg_match('/\d/', $input);
        $hasSpace = preg_match('/\s/', $input);
        $passes = $hasLetter && $hasDigit && $hasSpace;
        if (!$passes) {
            return "The [$field] field needs to be a valid street address";
        }
        return false;
    }

    /**
     * Validate is the string is valid IBAN
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_iban($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        static $character = array(
            'A' => 10, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15, 'G' => 16,
            'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21, 'M' => 22,
            'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27, 'S' => 28,
            'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
            'Z' => 35, 'B' => 11
        );
        if (!preg_match("/\A[A-Z]{2}\d{2} ?[A-Z\d]{4}( ?\d{4}){1,} ?\d{1,4}\z/", $input)) {
            return "The [$field] field needs to be a valid international bank account number";
        }
        $iban = str_replace(' ', '', $input);
        $iban = substr($iban, 4) . substr($iban, 0, 4);
        $iban = strtr($iban, $character);
        if (bcmod($iban, 97) != 1) {
            return "The [$field] field needs to be a valid international bank account number";
        }
        return false;
    }

    /**
     * Validate is the string is date format
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_date($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        $cdate1 = date('Y-m-d', strtotime($input));
        $cdate2 = date('Y-m-d H:i:s', strtotime($input));
        if ($cdate1 != $input && $cdate2 != $input) {
            return "The [$field] field needs to be a valid date";
        }
        return false;
    }

    /**
     * Validate minium age by date
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_min_age($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        $cdate1 = new DateTime(date('Y-m-d', strtotime($input)));
        $today = new DateTime(date('d-m-Y'));
        $interval = $cdate1->diff($today);
        $age = $interval->y;
        if ($age <= $param) {
            return "The [$field] field needs to have an age greater than or equal to [$param]";
        }
        return false;
    }

    /**
     * Validate if numeric value is maximum of given number
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_max_numeric($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (is_numeric($input) && is_numeric($param) && ($input <= $param)) {
            return false;
        }
        return "The [$field] field needs to be a numeric value, equal to, or lower than [$param]";
    }

    /**
     * Validate if numeric value is minimum of given number
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_min_numeric($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (is_numeric($input) && is_numeric($param) && ($input >= $param)) {
            return false;
        }
        return "The [$field] field needs to be a numeric value, equal to, or higher than [$param]";
    }

    /**
     * Validate if string starts with given value
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_starts_with($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (strpos($input, $param) !== 0) {
            return "The [$field] field needs to start with [$param]";
        }
        return false;
    }

    /**
     * Validate if file is valid image file
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_image($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (isset($input['tmp_name']) && $input['tmp_name'] !== '') {
            $imagesize = getimagesize($input['tmp_name']);
            if ($imagesize !== false) {
                return false;
            }
        }
        return "The [$field] needs to be a valid image file";
    }

    /**
     * Check if file type is in given types
     * @param string $field
     * @param Mixed $input
     * @param String $param
     * @return Mixed
     */
    protected function validate_file_type($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        if (isset($input['type'])) {
            $allowed_types = explode(";", $param);
            if (in_array($input['type'], $allowed_types)) {
                return false;
            }
        }
        return "The [$field] needs to be a file of type in [$param]";
    }

    /**
     * Validate Uploaded file size is less than limit
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_max_file_size($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        
        if (isset($input['size']) && $input['size'] <= ($param * 1024 * 1024)) {
            return false;
        }
        return "The [$field] needs to be of size less than [$param] MB";
    }
    
    
    /**
     * Validate Uploaded file is a valid file
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_valid_file($field, $input, $param = null){
        if($input === null){
            return false;
        }
        if (isset($input['error']) && $input['error'] > 0) {
            return "The [$field] needs to be a valid file";
        }
        return false;
    }

    /**
     * Validate Uploaded file extension is allowed
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_allowed_extension($field, $input, $param = null) {
        if($input === null){
            return false;
        }

        if (isset($input['error']) && $input['error'] !== 4) {
            $param = trim(strtolower($param));
            $allowed_extensions = explode(';', $param);

            $path_info = pathinfo($input['name']);
            $extension = $path_info['extension'];

            if (in_array($extension, $allowed_extensions)) {
                return false;
            }
        }
        return "The [$field] field can have the following extensions [$param]";
    }

    /**
     * Validate GUID V4
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     */
    protected function validate_guidv4($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        
        if (preg_match("/\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/", $input)) {
            return false;
        }
        return "The [$field] field needs to be a valid GUID V4";
    }

    /**
     * Validate Phone Number
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Mixed 
     *
     * Examples:
     *  555-555-5555: valid
     * 	5555425555: valid
     * 	555 555 5555: valid
     * 	1(519) 555-4444: valid
     * 	1 (519) 555-4422: valid
     * 	1-555-555-5555: valid
     * 	1-(555)-555-5555: valid
     */
    protected function validate_phone_number($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        
        $regex = '/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i';
        if (!preg_match($regex, $input)) {
            return "The [$field] field needs to be a valid phone number";
        }
        return false;
    }

    /**
     * Validate Regular Expression
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Array
     */
    protected function validate_regex($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        
        $regex = $param;
        if (!preg_match($regex, $input)) {
            return "The [$field] field needs to match [$param] regular expression";
        }
        return false;
    }

    /**
     * Check if value is valid json string
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return Array
     */
    protected function validate_valid_json($field, $input, $param = null) {
        if($input === null){
            return false;
        }
        
        if (!is_string($input) || !is_object(json_decode($input))) {
            return "The [$field] field needs to be a valid json string";
        }
        return false;
    }

    /**
     * Validate optional field do nothing
     * @param string $field
     * @param string $input
     * @param string $param optional
     * @return boolean
     */
    protected function validate_optional($field, $input, $param = null) {
        //do nothing
        return false;
    }

}
