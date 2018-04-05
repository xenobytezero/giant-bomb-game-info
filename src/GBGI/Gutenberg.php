<?php 

namespace GBGI;
require_gbgi_autoloader();

// -----------------------------------------------------------------

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

class Gutenberg {

    public static function register() {

        wp_register_script(
            'gbgi-gutenberg-block',
            plugins_url('../../dist/gutenberg/block.js', __FILE__),
            [
                'wp-blocks', 
                'wp-element',
                'wp-components'
            ]
        );

        wp_register_style(
            'gbgi-gutenberg-editor-style',
            plugins_url('../../dist/gutenberg/block/style-editor.css', __FILE__)
        );

        register_block_type( 
            'gbgi/gbgi-block', [
                'editor_script' => 'gbgi-gutenberg-block',
                'editor_style' => 'gbgi-gutenberg-editor-style'
            ]
        );

    }


}



?>