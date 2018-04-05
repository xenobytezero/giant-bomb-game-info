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

    }

    public static function handle_api_key() {
        return Common::get_api_key();
    }



}

?>