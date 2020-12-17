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

        if (defined("BUGSNAG_KEY") && BUGSNAG_KEY) {
            $bugsnag = Bugsnag\Client::make(BUGSNAG_KEY);
            $bugsnag->notifyError("shutdown_error", json_encode($e));
        }


        if (defined("DEBUG_MODE") && DEBUG_MODE) {
            $output = MaskAPI\Helper\Response::response_object(true, 500, $e);

            MaskAPI\Helper\Response::response(500, $output);
        } else {
            $output = MaskAPI\Helper\Response::response_object(true, 500, ['server error']);
            MaskAPI\Helper\Response::response(500, $output);
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

    if (defined("BUGSNAG_KEY") && BUGSNAG_KEY) {
        $bugsnag = Bugsnag\Client::make(BUGSNAG_KEY);
        $bugsnag->notifyError("error", json_encode($error));
    }

    if (defined("DEBUG_MODE") && DEBUG_MODE) {
        $output =  MaskAPI\Helper\Response::response_object(true, 500, $error);
        MaskAPI\Helper\Response::response(500, $output);
    } else {
        $output = MaskAPI\Helper\Response::response_object(true, 500, ['server error']);
        MaskAPI\Helper\Response::response(500, $output);
    }
}, E_ALL);

/**
 * exception handler
 * @param type $ex
 */
function exception_handler($ex) {

    if (defined("DEBUG_MODE") && DEBUG_MODE) {
        $exception = ['exception' => [
                'message' => $ex->getMessage(),
                'code' => $ex->getCode(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine()
        ]];

        if (defined("BUGSNAG_KEY") && BUGSNAG_KEY) {
            $bugsnag = Bugsnag\Client::make(BUGSNAG_KEY);
            $bugsnag->notifyError("error", json_encode($exception));
        }

        $output = MaskAPI\Helper\Response::response_object(true, 500, $exception);
        MaskAPI\Helper\Response::response(500, $output);
        
    } else {
        $output = MaskAPI\Helper\Response::response_object(true, 500, ['server exception']);
        MaskAPI\Helper\Response::response(500, $output);
    }
}

set_exception_handler("exception_handler");




