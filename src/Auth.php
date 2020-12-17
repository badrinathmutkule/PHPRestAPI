<?php

/**
 * The Auth Abstract class
 * @author Badrinath Mutkule <badrinath.mutkule@gmail.com>
 * @version 1.0.0 [Beta]
 */

namespace MaskAPI;
use MaskAPI\Core\Methods;

abstract class Auth extends Methods {
    
    /**
     * @validate method should return response object
     */
    public abstract function validate();
}
