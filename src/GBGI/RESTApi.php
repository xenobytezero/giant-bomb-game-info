<?php 

namespace GBGI;

// -----------------------------------------------------------------

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

class RESTApi {

    // ---------------------------------------------------

    public static function register_api() {

        register_rest_route('gbgi/v1', '/apiKey', [
            'methods' => 'GET',
            'callback' => function() {
                return RESTApi::get_api_key_from_db();
            },
            'permission_callback' => function () {
              return current_user_can('manage_options');
            }
        ]);

        register_rest_route('gbgi/v1', '/apiKey', [
            'methods' => 'POST',
            'callback' => function($request) {
                $params = $request->get_params();
                return RESTApi::set_api_key_to_db($params['apiKey']);
            },
            'permission_callback' => function () {
              return current_user_can('manage_options');
            }
        ]);

        register_rest_route('gbgi/v1', '/apiKey', [
            'methods' => 'DELETE',
            'callback' => function() {
                return RESTApi::delete_api_key();
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


    // ---------------------------------------------------
    // ---------------------------------------------------
    // ---------------------------------------------------

    private static function get_api_key_from_db(){
        ///debug_print_backtrace();
        $enc_api_key = get_option(Common::$OPTION_NAME);
        $api_key = null;
        if ($enc_api_key !== false){
            $api_key = Common::$encryption_helper->dec($enc_api_key);
        }
        return $api_key;
    }
    
    private static function set_api_key_to_db($api_key){
        $enc_api_key = Common::$encryption_helper->enc($api_key);
        update_option(Common::$OPTION_NAME, $enc_api_key);
    }

    private static function delete_api_key() {
        delete_option(Common::$OPTION_NAME);
    }

    // ---------------------------------------------------

    public static function handle_templates() {

        $available_templates = \WidgetSupport\TemplateDiscovery::find_templates(
            Common::get_custom_template_base()
        );

        $template_opts = array_map(function($tmpl) {
            return [
                "name" => $tmpl["name"],
                "value" => $tmpl["path"]
            ];
        }, $available_templates);

        return $template_opts;

    }


}

?>