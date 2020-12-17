<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MaskAPI\Doc;

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
            @mkdir($target);
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
    
    
    
    public function generate($target){
        $source =  __DIR__ . DIRECTORY_SEPARATOR . "swaggerUi";
        $this->copy_directory($source, $target);
    }

}
