<?php

namespace GBGI;

// -----------------------------------------------------------------

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

class Common {

    public static $OPTION_GROUP = 'gbgi';
    public static $OPTION_NAME = 'gbgi_api_key';
    public static $META_KEY = 'gbgi_gameinfo';

    private static $PLUGIN_BASE_DIR_CACHE = null;
    private static $PLUGIN_BASE_URL_CACHE = null;

    public static $encryption_helper = null;

    public static function init() {

        Common::$encryption_helper = new \WidgetSupport\Encryption(
            realpath(dirname(__FILE__) . "/../.."),
            'GBGI_ENC_KEY'
        );

    }

    private static function update_base_dir_cache() {
        if (Common::$PLUGIN_BASE_DIR_CACHE == null){
            Common::$PLUGIN_BASE_DIR_CACHE = 
                realpath(dirname(__FILE__) . "/../../");
        }
    }
    private static function update_base_url_cache() {
        if (Common::$PLUGIN_BASE_URL_CACHE == null){
            Common::$PLUGIN_BASE_URL_CACHE = 
                plugin_dir_url(__DIR__ . "/../../..");
        }
    }

    public static function plugin_url($file = ''){
        Common::update_base_url_cache();
        return Common::$PLUGIN_BASE_URL_CACHE . $file;
    }

    public static function plugin_file($file = '') {
        Common::update_base_dir_cache();
        return Common::$PLUGIN_BASE_DIR_CACHE . "/" . $file;        

    }

    public static function get_custom_template_base() {
        return get_template_directory() . '/templates/giant-bomb-game-info';
    }

    public static function register_meta() {
        register_meta( 'post', Common::$META_KEY, [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ]);
    }

    public static function register_settings() {
        register_setting(
            Common::$OPTION_GROUP, 
            Common::$OPTION_NAME
        );
    }

}

Common::init();

?>