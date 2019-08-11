<?php

// autoload.php @generated by Composer
$env = $_SERVER['DOCUMENT_ROOT'].'/vendor/env.php';
if(file_exists($env)){
    require_once $env;
}

if(!function_exists('env')){
    function env($key, $default = null){
        $value = getenv($key);
        if($value==false)
            return $default;
        return $value;
    }
}

require_once __DIR__ . '/composer/autoload_real.php';

return ComposerAutoloaderInitc70e22a105ab464ceddda8512d2ba98e::getLoader();
