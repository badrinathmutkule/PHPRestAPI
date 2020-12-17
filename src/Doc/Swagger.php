<?php

/**
 * The Swagger library for swagger documentation 
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace MaskAPI\Doc;

use Symfony\Component\Yaml\Yaml;

class Swagger {

    private $info = [];
    private $host = 'example.com';
    private $base_path = '';
    private $schemes = [];
    private $consumes = ['application/json'];
    private $produces = ['application/json'];
    private $paths = [];
    private $definations = [];
    private $route_file = null;

    /**
     * Set API Info
     * @param array $info
     */
    public function set_info(array $info) {
        $this->info = $info;
    }

    /**
     * Set Host
     * @param string $host
     */
    public function set_host($host) {
        $this->host = strtolower($host);
    }

    /**
     * Set Base Path
     * @param string $basepath
     */
    public function set_base_path($basepath) {
        $this->base_path = strtolower($basepath);
    }

    /**
     * set Schema
     * @param string $schema
     */
    public function set_schema($schema) {
        $this->schemes[] = strtolower($schema);
    }

    /**
     * Set Route file
     * @param type $route_file
     */
    public function set_route_file($route_file) {
        $this->route_file = $route_file;
    }

    /**
     * Generate Swagger  v2 json file 
     * @param string $output output file name
     */
    public function generate($output) {
        if (php_sapi_name() !== "cli") {
            echo 'Allowed only through command line' . PHP_EOL;
            return false;
        }
        $route_file = $this->route_file;
        if ($route_file == null || !file_exists($route_file)) {
            echo 'Route file not found' . PHP_EOL;
            return false;
        }
        $routes = Yaml::parse(file_get_contents($route_file));
        if (!is_array($routes)) {
            echo "Invalid or empty route file" . PHP_EOL;
            return false;
        }
        $this->parse_routes($routes);
        $json = $this->generate_final_json();
        file_put_contents($output, $json);
        echo "success!" . PHP_EOL;
    }

    /**
     * Parse routes
     * @param array $routes
     */
    private function parse_routes($routes) {

        foreach ($routes as $route) {
            if (!isset($this->paths[$route['url']])) {
                $this->paths[$route['url']] = [];
            }
            $key = $route['method'] . ':' . $route['url'];
            $operation_id = $this->get_operation_id($key);
            $description = $this->get_description($key);
            $path = [];
            $path['operationId'] = $operation_id;
            $path['description'] = str_replace('"', '', $description);
            $path['produces'] = ['application/json'];
            $path['security'] = [];
            $path['tags'] = $this->get_tags($route['controller']);
            $path['x-unitTests'] = [];
            $path['x-operation-settings'] = [
                'CollectParameters' => false,
                'AllowDynamicQueryParameters' => false,
                'AllowDynamicFormParameters' => false,
                'IsMultiContentStreaming' => false
            ];
            $path['parameters'] = $this->get_parameters($operation_id, $route);

            $hasFile = $this->has_file($route);
            if($hasFile){
                $path['consumes'] = ["multipart/form-data"];
            }
            $path["responses"] = [
                "200" => [
                    "description" => ""
                ]
            ];

            $this->paths[$route['url']][$route['method']] = $path;
        }
    }

    /**
     * Get tags from class path
     * @param string $classpath
     * @return array
     */
    private function get_tags($classpath) {
        $parts = explode('\\', $classpath);
        $last = strtoupper(array_pop($parts));
        return [$last];
    }

    /**
     * Get request parameters
     * @param string $operation_id
     * @param array $route_info
     * @return array
     */
    private function get_parameters($operation_id, $route_info) {
        $parameters = [];
        if (isset($route_info['auth']) && !empty($route_info['auth'])) {
            foreach ($route_info['auth'] as $key => $value) {
                $parameters[] = [
                    "name" => $key,
                    "in" => "header",
                    "required" => true,
                    "type" => "string"
                ];
            }
        }
        if (isset($route_info['validation']) && !empty($route_info['validation'])) {
            if ($route_info['method'] == 'get') {
                foreach ($route_info['validation'] as $key => $value) {
                    $typearray = explode("|", $value);
                    $is_required = in_array("optional", $typearray) ? false : true;

                    $param = [
                        "name" => $key,
                        "in" => "query",
                        "required" => $is_required,
                    ];
                    if (in_array('numeric', $typearray) || in_array("integer", $typearray)) {
                        $param['type'] = 'integer';
                        $param['format'] = 'int32';
                    } else {
                        $param['type'] = 'string';
                    }
                    $parameters[] = $param;
                }
            } else {
                $hasFile = $this->has_file($route_info);
                if ($hasFile) {
                    foreach ($route_info['validation'] as $key => $value) {
                        $typearray = explode("|", $value);
                        $is_required = in_array("optional", $typearray) ? false : true;
                        $param_type = (in_array("valid_image", $typearray) || in_array("valid_file", $typearray)) ? "file" : "string";
                        $parameters[] = [
                            "name" => $key,
                            "in" => "formData",
                            "required" => $is_required,
                            "type" => $param_type,
                            "format" => $param_type
                        ];
                    }
                } else {
                    $param_key = $this->create_key($operation_id);
                    $body_params = [
                        "title" => $operation_id . ' Request',
                        "type" => "object"
                    ];
                    $properties = [];
                    $required = [];
                    foreach ($route_info['validation'] as $key => $value) {
                        $typearray = explode("|", $value);
                        if (in_array('numeric', $typearray) || in_array("integer", $typearray)) {
                            $param_type = 'integer';
                        } else {
                            $param_type = 'string';
                        }
                        
                        $properties[$key] = ['type' => $param_type];
                        $required[] = $key;
                    }
                    $body_params['properties'] = $properties;
                    $body_params['required'] = $required;

                    $this->definations[$param_key] = $body_params;
                    $param = [
                        "name" => "Body",
                        "in" => "body",
                        "required" => true,
                        "schema" => [
                            '$ref' => '#/definitions/' . $param_key
                        ]
                    ];

                    $parameters[] = $param;
                }
            }
        }

        return $parameters;
    }

    /**
     * Create Key
     * @param string $operation_id
     * @return string
     */
    private function create_key($operation_id) {
        $strarray = explode(" ", $operation_id);
        for ($i = 0; $i < count($strarray); $i++) {
            $strarray[$i] = ucfirst(strtolower($strarray[$i]));
        }
        return implode("", $strarray) . "Request";
    }

    /**
     * generate final swagger json string
     * @return string
     */
    private function generate_final_json() {
        $final = [
            'swagger' => '2.0',
            'info' => $this->info,
            'host' => $this->host,
            'basePath' => $this->base_path,
            'schemes' => $this->schemes,
            'consumes' => $this->consumes,
            'produces' => $this->produces,
            'paths' => $this->paths,
            'definitions' => $this->definations
        ];
        return json_encode($final);
    }

    /**
     * Get operation id
     * @param string $path
     * @return string
     */
    private function get_operation_id($path) {
        $array = [];
        $exploded = explode(":", $path);
        $array[] = ucfirst($exploded[0]);
        if (isset($exploded[1])) {
            $s = explode("/", $exploded[1]);
            foreach ($s as $a) {
                $array[] = ucfirst($a);
            }
        }
        $id = implode("", $array);
        return $id;
    }

    /**
     * Get method description
     * @param string $path
     * @return string
     */
    private function get_description($path) {
        $string = '';
        $exploded = explode(":", $path);
        $string .= strtoupper($exploded[0]) . ' method for -';
        if (isset($exploded[1])) {
            $s = explode("/", $exploded[1]);
            foreach ($s as $a) {
                $string .= $a . ' ';
            }
        }
        return $string;
    }

    /**
     * Check if this route is for file upload
     * @param array $route
     * @return boolean 
     */
    private function has_file($route){
        $hasFile = false;

        if(is_null($route['validation'])){
            return false;
        }
        
        foreach ($route['validation'] as $key => $value) {
            $typearray = explode("|", $value);
            if (in_array("valid_image", $typearray) || in_array("valid_file", $typearray)){
                $hasFile = true;
            }
        }
        return $hasFile;
    }

}
