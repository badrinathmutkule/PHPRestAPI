<?php

/**
 * The Auth Abstract class
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace PHPRestFramework;

abstract class Auth extends Core {
    
    /**
     * @validate method should return response object
     */
    public abstract function validate();
}
