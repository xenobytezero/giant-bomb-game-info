<?php

spl_autoload_register(function($class_name) {

    $default_namespace = "GBGI";

    // If the specified $class_name does not include our namespace, duck out.
    if (false === strpos($class_name, $default_namespace . "\\")) {
        //echo "<p>GBGI - Class Not Found - " . $class_name . "</p>";
        return;
    }

    $path = plugin_dir_path(__FILE__) . $class_name . '.php';
    $path = str_ireplace('/', '\\', $path);

    if ( file_exists( $path ) ) {
        include_once( $path );
    } else {
        echo("Cannot Find File for class " . $class_name . " Not Found - " . $path);
    }
    
});

?>