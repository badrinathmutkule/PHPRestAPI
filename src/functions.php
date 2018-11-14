<?php

/**
 * All important functions
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */
function shutdown_handler() {
    $isError = false;

    if ($error = error_get_last()) {
        switch ($error['type']) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $isError = true;
                break;
        }
    }
    if ($isError) {
        $e = ['error' => [
                'type' => $error['type'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line']
        ]];

        if (defined("BUGSNAG_KEY")) {
            $bugsnag = Bugsnag\Client::make(BUGSNAG_KEY);
            $bugsnag->notifyError("shutdown_error", json_encode($e));
        }


        if (defined("DEBUG_MODE") && DEBUG_MODE) {
            $output = response_object(true, 500, $e);
            response(500, $output);
        } else {
            $output = response_object(true, 500, ['server error']);
            response(500, $output);
        }
    }
}

register_shutdown_function('shutdown_handler');

/**
 * Set error hander
 * error handler function 
 */
set_error_handler(function($type, $message, $file, $line) {

    $error = ['error' => [
            'type' => $type,
            'message' => $message,
            'file' => $file,
            'line' => $line
    ]];

    if (defined("BUGSNAG_KEY")) {
        $bugsnag = Bugsnag\Client::make(BUGSNAG_KEY);
        $bugsnag->notifyError("error", json_encode($error));
    }

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

        if (defined("BUGSNAG_KEY")) {
            $bugsnag = Bugsnag\Client::make(BUGSNAG_KEY);
            $bugsnag->notifyError("error", json_encode($exception));
        }

        $output = response_object(true, 500, $exception);
        response(500, $output);
    } else {
        $output = response_object(true, 500, ['server exception']);
        response(500, $output);
    }
}

set_exception_handler("exception_handler");

/**
 * Response object function
 * @param boolean $error true or palse value for error
 * @param int $code an error or success code
 * @param array $message 
 * @param array $data optional
 * return final array
 */
function response_object($error, $code, array $message, $data = array()) {

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

function response($status, array $data) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Origin, Content-Type, AuthToken, ApiKey, AccessToken");

    header("Content-Type: application/json; charset=UTF-8");
    header("x-content-type-options: nosnif");
    header("x-xss-protection: 1; mode=block");
    header("x-frame-options: sameorigin");
    header("cache-control: no-cache, no-store, max-age=0, must-revalidate");

    http_response_code($status);
    $data['process_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    echo json_encode($data);
    exit;
}
