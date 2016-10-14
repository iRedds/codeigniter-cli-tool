<?php

/**
 *  WiseCI autoload
 */

spl_autoload_register(function($class){

    if (strpos($class, 'Wisecore\\') === 0) {
        $path = dirname(__DIR__) . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    } elseif (strpos($class, 'CI_') === 0) {
        $path = BASEPATH . 'core/' . str_replace('CI_', '', $class) . '.php';
        if (file_exists($path)) {
            require_once $path;
        } else {
            $path = BASEPATH . 'libraries/' . str_replace('CI_', '', $class) . '.php';
            if (file_exists($path)) {
                require_once $path;
            }
        }
    }
});

function &get_instance()
{
    return \CI_Controller::get_instance();
}