<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 2:03 AM
 *
 * function to autoload classes
 * @author marthaler
 */

/**
 * Function autoloads classes in libraries
 * @param $classname
 */
function autoload ($classname) {
    $base = $_SERVER['DOCUMENT_ROOT']."/property-manager/libraries";
    $path = "";

    if (preg_match("/\\\\/",$classname)) {
        $path .= str_replace('\\',DIRECTORY_SEPARATOR, $classname);
    } else {
        $path .= str_replace('_',DIRECTORY_SEPARATOR, $classname);
    }

    require_once($base."/".$path.".php");
}
spl_autoload_register("autoload");