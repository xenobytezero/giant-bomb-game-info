<?php
/*
Plugin Name: Giant Bomb Game Info
Plugin URI: 
Description: 
Version: @@releaseVersion
Author: Euan Robertson
Author Email: xenobytezero@gmail.com
License:
*/

defined( 'ABSPATH' ) or die;

// ---------------------------------------------------------------

require_once('vendor/autoload.php');

require_once('src/GBGI/Common.php');
require_once('src/GBGI/RESTApi.php');
require_once('src/GBGI/Gutenberg.php');

// ----------------------------------------------------------------
// Timber/Twig Setup

// add 'gbgi' namespace for templates
add_filter('timber/loader/loader', function($loader){
	$loader->addPath(__DIR__ . "/templates", "gbgi");
	$loader->addPath(GBGI\Common::get_custom_template_base(), "gbgi-custom-template");
	return $loader;
});

// -----------------------------------------------------------------

add_action('init', ['GBGI\Gutenberg', 'register']);
add_action('enqueue_block_editor_assets', ['GBGI\Gutenberg', 'enqueue']);

add_action('rest_api_init', ['GBGI\RESTApi', 'register_api']);
    
add_action('init', ['GBGI\Common', 'register_meta']);

add_action('admin_init', ['GBGI\Common', 'register_settings']);

// -----------------------------------------------------------------

?>