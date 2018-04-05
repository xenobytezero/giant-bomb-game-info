<?php

namespace GBGI;
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
            'GBGIOptions::create_admin_menu'
        );
    }

    public static function create_admin_menu() {

        $context = [
            'option_group' => Common::$OPTION_GROUP,
            'option_name' => Common::$OPTION_NAME,
            'options' => get_option(Common::$OPTION_NAME)
        ];

        Timber::render('templates/admin_menu.twig', $context);
    }

}

?>