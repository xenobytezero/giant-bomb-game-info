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
            Common::plugin_url('dist/block.dist.js'),
            [
                'wp-blocks', 
                'wp-element',
                'wp-components'
            ]
        );

        wp_register_style(
            'gbgi-gutenberg-style',
            Common::plugin_url('dist/block/style.css')
        );

        wp_register_style(
            'gbgi-gutenberg-editor-style',
            Common::plugin_url('dist/block/style.css')
        );

        register_block_type( 
            'gbgi/gbgi-block', [
                'style' => 'gbgi-gutenberg-style',
                'editor_script' => 'gbgi-gutenberg-block',
                'editor_style' => 'gbgi-gutenberg-editor-style',
                'render_callback' => '\GBGI\Gutenberg::render_block'
            ]
        );

    }

    public static function render_block($attrs){

        $disable_render = array_key_exists('disableRender', $attrs) ?
            $attrs['disableRender'] :
            false;

        $game_info_json = array_key_exists('gameInfoJson', $attrs) ? 
            $attrs['gameInfoJson'] :
            "";

        $custom_template = array_key_exists('customTemplate', $attrs) ? 
            $attrs['customTemplate'] :
            "";

            
        if ($disable_render){
            return  "<!-- gbgi/gbgi-block - Block Render Disabled -->";
        }

        if ($game_info_json == ''){
            return  "<!-- gbgi/gbgi-block - No Game Data -->";
        } 


        $decoded_json = json_decode($game_info_json, true);

        $template = '@gbgi/block_default.twig';

        if ($custom_template !== ''){
            $template =  "@gbgi-custom-template/" . $custom_template;
        }

        $context = [
            'game_info' => $decoded_json,
            'gb_icon_path' => Common::plugin_url('assets/gb.png')
        ];

        $comment_wrapper = "<!-- gbgi/gbgi-block - " . $template . " -->";

        return $comment_wrapper . 
            \Timber::compile($template, $context) . 
            $comment_wrapper;

    }


}



?>