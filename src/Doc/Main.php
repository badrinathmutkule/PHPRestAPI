<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MaskAPI\Doc;
use MaskAPI\Doc\Swagger;

class Main {

    /**
     * Constructor
     */
    public function __construct() {
        //constructor;
    }

    
    /**
     * Copy entire directory with all its content
     * @param type $source
     * @param type $target
     */
    public function copy_directory($source, $target) {
        
        if (is_dir($source)) {

            if(!file_exists($target)){
                @mkdir($target);
            }

            $d = dir($source);
            while (FALSE !== ( $entry = $d->read() )) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $Entry = $source . '/' . $entry;
                if (is_dir($Entry)) {
                    $this->copy_directory($Entry, $target . '/' . $entry);
                    continue;
                }
                copy($Entry, $target . '/' . $entry);
            }

            $d->close();
        } else {
            copy($source, $target);
        }
    }
    
    
    /**
     * Generate swagger document
     * @param type $target
     */
    public function generate($target){

        echo "generating swagger documentation!". PHP_EOL;

        $source =  __DIR__ . DIRECTORY_SEPARATOR . "swaggerUi";
        $publicDir = $target . DIRECTORY_SEPARATOR . PUBLIC_DIRECTORY;
        $docDir = $publicDir . DIRECTORY_SEPARATOR . "doc";
        if(!file_exists($publicDir)){
            mkdir($publicDir);
        }

        if(!file_exists($docDir)){
            mkdir($docDir);
        }

        $this->copy_directory($source, $docDir);

        $swagger = new Swagger();
        $swagger->set_base_path("/");
        $swagger->set_route_file(ROUTE_FILE);
        $swagger->set_host(API_ENDPOINT);
        $swagger->set_schema(IS_HTTPS);

        $swagger->set_info([
            "version" => API_VERSION,
            "title" => API_TITLE,
            "description" => API_TITLE ." api documentation",
            "license" => [
                "name" => "MIT",
                "url" => "http://github.com/gruntjs/grunt/blob/master/LICENSE-MIT"
            ],
            "contact" => [
                "name" => API_CONTACT_NAME,
                "url" => API_CONTACT_URL,
                "email" => API_CONTACT_EMAIL
            ]
        ]);

        $swaggerFilePath = $docDir . DIRECTORY_SEPARATOR ."swagger.json";
        $swagger->generate($swaggerFilePath);

    }






}
