<?php

namespace GBGI;
// -----------------------------------------------------------------

require_gbgi_autoloader();

// -----------------------------------------------------------------

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

class Options {

    public static function register_admin_menu() {
        add_options_page(
            'Giant Bomb Game Info',
            'Giant Bomb Game Info',
            'manage_options',
            'giant-bomb-game-info',
            ['GBGI\Options', 'create_admin_menu']
        );
    }

    public static function create_admin_menu() {

        wp_enqueue_script('gbgi_options_page');
        
        $opts = get_option(Common::$OPTION_NAME);

        $apiKey = $opts['apiKey'] != '' ?
            Common::dec($opts['apiKey']) :
            null;

        $context = [
            'option_group' => Common::$OPTION_GROUP,
            'option_name' => Common::$OPTION_NAME,
            'api_key' => $apiKey
        ];

        \Timber::render('@gbgi/admin_menu.twig', $context);
    }

}

?>