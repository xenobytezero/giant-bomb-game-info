<?php 

namespace GBGI;
require_gbgi_autoloader();

// -----------------------------------------------------------------

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

class RESTApi {

    public static function register_api() {

        register_rest_route('gbgi/v1', '/apiKey', [
            'methods' => 'GET',
            'callback' => function() {
                return RESTApi::handle_api_key();
            },
            'permission_callback' => function () {
              return current_user_can('manage_options');
            }
        ]);

        register_rest_route('gbgi/v1', '/templates', [
            'methods' => 'GET',
            'callback' => function() {
                return RESTApi::handle_templates();
            },
            'permission_callback' => function () {
              return current_user_can('manage_options');
            }
        ]);

    }

    public static function handle_api_key() {
        return Common::get_api_key();
    }

    public static function handle_templates() {

        // get the template base dir
        $custom_template_base = Common::get_custom_template_base();

        $files = [
            ['name' => 'Default Template', 'path' => '']
        ];

        if (file_exists($custom_template_base)){
            // find all files in that dir
            $template_paths = array_diff(scandir($custom_template_base), array('..', '.'));
            
            foreach($template_paths as $template){
                array_push($files,[
                    'name' => $template,
                    'path' => $template
                ]);
            }
        }

        return $files;

    }


}

?>