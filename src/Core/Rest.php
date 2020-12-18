<?php

/**
 * The Rest class
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace MaskAPI\Core;

use Symfony\Component\Yaml\Yaml;
use MaskAPI\Helper\Input;
use MaskAPI\Helper\Response;

class Rest {

    private $routes;

    /**
     * 
     * @param string $routefile
     * @throws Exception
     */
    public function __construct($routefile) {
        if (file_exists($routefile)) {
            $this->routes = Yaml::parse(file_get_contents($routefile));
        } else {
            throw new \Exception("Invalid route file");
        }
    }

    /**
     * Map request to a route
     * @return boolean
     */
    public function mapRequest() {
        $routes = [];
        foreach ($this->routes as $route) {
            $key = $route["method"] . ":" . $route["url"];
            $routes[$key] = $route;
        }
        $pattern = Input::pattern();
        $method = Input::method();
        $path = strtolower($method . ":" . $pattern);

        if (array_key_exists($path, $routes)) {
            return $routes[$path];
        }
        return false;
    }
    
    /**
     * Serve requests
     */
    public function serve() {
        
        $mapped = $this->mapRequest();
        
        if ($mapped === false) {
            $response = Response::response_object(true, 500, ['Method not found']);
            Response::response(404, $response);
        }
        
        $valid = $this->validateAuth($mapped);
        if (!empty($valid)) {
            Response::response(401, $valid);
        }
        
        $errors = $this->runValidations($mapped);
        if (!empty($errors)) {
            $response = Response::response_object(true, 500, $errors);
            Response::response(400, $response);
        }

        $namespace = $this->getNs($mapped['controller']);
        if (class_exists($namespace) && method_exists($namespace, $mapped['action'])) {
            $instance = new $namespace;
            $result = $instance->{$mapped['action']}();
            
            if (!$this->isResponseObject($result)) {
                $response = Response::response_object(true, 500, ['Method should return response object']);
                Response::response(500, $response);
            }
            
            Response::response(200, $result);
        }
        
        $response = Response::response_object(true, 500, ['Controller and/or action not found']);
        Response::response(500, $response);
        
    }

    /**
     * Validate Auth from mapped request
     * @param array $mapped_route
     * @return Array
     */
    private function validateAuth($mapped_route) {
        $auth = isset($mapped_route['auth']) ? $mapped_route['auth'] : [];
        foreach ($auth as $key => $value) {
            $namespace = $this->getNs($value);
            if (class_exists($namespace) && method_exists($namespace, 'validate')) {
                $instance = new $namespace;
                $valid = $instance->validate();
                if (!is_array($valid) || !isset($valid['error'])) {
                    return Response::response_object(true, 500, ["Auth method [$key] should return response object"]);
                } else {
                    if ($valid['error'] === true) {
                        return $valid;
                    }
                }
            } else {
                return Response::response_object(true, 500, ["Invalid auth method [$key]"]);
            }
        }
        return [];
    }

    /**
     * Create Namespace from controller
     * @param string $controller
     * @return string Namespace
     */
    private function getNs($controller) {
        return str_replace("/", "\\", $controller);
    }

    /**
     * Run Filters and validations
     * @param array $mapped_route
     * @return array
     */
    private function runValidations($mapped_route) {
        $input = Input::param();
        $validations = !isset($mapped_route['validation']) ? [] : $mapped_route['validation'];
        $v = new \MaskAPI\Validation();
        return $v->run($input, $validations);
    }

    /**
     * Check if response is valid response object
     * @param type $result
     * @return boolean
     */
    private function isResponseObject($result) {
        if (!is_array($result) || !isset($result['error']) || !isset($result['message']) || !isset($result['code'])) {
            return false;
        }
        return true;
    }

}
