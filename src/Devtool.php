<?php
/**
 * The Auth Abstract class
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */


namespace MaskAPI;

use MaskAPI\Doc\Main;

class Devtool {

    /**
     * Constructor
     */
    public function __construct(){
        //constructor
    }


    /**
     * 
     */
    public function run($path = false){

        $command = isset($argv[1]) ? $argv[1] : false;
        $param = isset($argv[2]) ? $argv[2] : false;

        if($command == "init"){

            $source = __DIR__ . DIRECTORY_SEPARATOR . "config.php";
            $target = $path . DIRECTORY_SEPARATOR . "config.development.php";
            \copy($source, $target);

            echo "configuration file generated!".PHP_EOL;

        }elseif($command == "doc"){

            if(defined("ROUTE_FILE") && defined("PUBLIC_DIRECTORY")){
                $main = new Main();
                $main->generate($path);
                
            }else {
                echo "Failed - please make sure configuration file is valid!" . PHP_EOL;
            }

        }else{
            echo "invalid command!" . PHP_EOL;
        }


    }
    
}