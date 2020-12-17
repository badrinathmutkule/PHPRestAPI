<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'Doc/Main.php';

$main = new MaskAPI\Doc\Main();

$target = __DIR__ . DIRECTORY_SEPARATOR . "spec";
$main->generate($target);