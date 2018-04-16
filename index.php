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

// ----------------------------------------------------------------
// Timber/Twig Setup

// add 'gbgi' namespace for templates
add_filter('timber/loader/loader', function($loader){
	$loader->addPath(__DIR__ . "/templates", "gbgi");
	$loader->addPath(GBGI\Common::get_custom_template_base(), "gbgi-custom-template");
	return $loader;
});

// -----------------------------------------------------------------

register_activation_hook(__FILE__, ['GBGI\Common', 'create_encryption_key']);

add_action( 'init', ['GBGI\Gutenberg', 'register']);
add_action( 'rest_api_init', ['GBGI\RESTApi', 'register_api']);
    
add_action( 'init', ['GBGI\Common', 'register_meta']);

add_action('admin_init', ['GBGI\Common', 'register_settings']);
add_action('admin_menu', ['GBGI\Options', 'register_admin_menu']);

add_action('admin_enqueue_scripts', ['GBGI\Common', 'register_scripts']);

// -----------------------------------------------------------------

function require_gbgi_autoloader(){
    require_once(plugin_dir_path(__FILE__) . "src/Autoloader.php");
}

?>