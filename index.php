<?php
/*
Plugin Name: Giant Bomb Game Info Widget
Plugin URI: 
Description: 
Version: @@releaseVersion
Author: Euan Robertson
Author Email: xenobytezero@gmail.com
License:
*/

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

require_gbgi_autoloader();

// -----------------------------------------------------------------

add_action( 'init', ['GBGI\Gutenberg', 'register']);
add_action( 'rest_api_init', ['GBGI\RESTApi', 'register_api']);
    
add_action( 'init', ['GBGI\Common', 'register_meta']);

// -----------------------------------------------------------------

function require_gbgi_autoloader(){
    require_once(plugin_dir_path(__FILE__) . "src/Autoloader.php");
}


?>

