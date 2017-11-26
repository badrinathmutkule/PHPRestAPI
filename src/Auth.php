<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PHPRestFramework;

abstract class Auth extends Core {
    
    /**
     * @validate method should return response object
     */
    public abstract function validate();
}
