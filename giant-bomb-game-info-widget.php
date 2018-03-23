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

require_once('vendor/widget-support/widget-support.php');

// Register the widget
add_action('widgets_init', function(){
    register_widget("GiantBombGameInfoWidget");
});

class GiantBombGameInfoWidget extends \WP_Widget {

    private $widget_path;
    
    private $default_args = [

    ];

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {

        $this->widget_path = dirname(__FILE__);
            
        $widget_ops = array(
            'description' => 'Attach game info to a post, pulled from the Giant Bomb API',
        );

        parent::__construct('giant-bomb-game-info-widget', 'Giant Bomb Game Info', $widget_ops);

        //add_action('wp_ajax_pgposthighlight_test', [$this, 'handle_test_ajax']);

        // Register the resources for this widget
        //add_action('init', [$this, 'register_widget_resources']);

    }

/*
    public function handle_test_ajax() {
        echo "it works";
        wp_die();
    }
*/
    public function register_widget_resources() {
        /*
        wp_register_script(
            'giant-bomb-game-info-widget-admin.js',
            $this->widget_path . '/pg-post-highlight-admin.js'        
        );
        */
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        //\Timber::render($this->widget_path + 'gb-info-widget.twig');
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

        //wp_enqueue_script('pg_post_highlight_admin_js');

        // get the values mixed with the defaults
        $opts = wp_parse_args($instance, $this->default_args);

        echo "<!-- GBGIW -->";


        echo "<!-- GBGIW -->";

    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        return $new_instance;
    }


}

?>