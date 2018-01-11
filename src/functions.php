<?php
/**
 * All important functions
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

set_error_handler(function($type, $message, $file, $line) {

    $error = ['error' => [
            'type' => $type,
            'message' => $message,
            'file' => $file,
            'line' => $line
    ]];

    if (defined("DEBUG_MODE") && DEBUG_MODE) {
        $output = response_object(true, 500, $error);
        response(500, $output);
    } else {
        $output = response_object(true, 500, ['server error']);
        response(500, $output);
    }
}, E_ALL);

function exception_handler($ex) {
    if (defined("DEBUG_MODE") && DEBUG_MODE) {
        $exception = ['exception' => [
                'message' => $ex->getMessage(),
                'code' => $ex->getCode(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine()
        ]];
        $output = response_object(true, 500, $exception);
        response(500, $output);
    } else {
        $output = response_object(true, 500, ['server exception']);
        response(500, $output);
    }
}

set_exception_handler("exception_handler");

function response_object($error, $code, array $message, $data = array()) {
    
    $finalData = [
        'error' => $error,
        'code' => $code,
        'message' => $message
    ];
    
    if(!empty($data)){
        $finalData['data'] = $data;
    }
    return $finalData;
}

function response($status, array $data) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Origin, Content-Type, AuthToken, ApiKey, AccessToken");
    header("Content-Type: application/json");

    http_response_code($status);
    $data['process_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    echo json_encode($data);
    exit;
}
