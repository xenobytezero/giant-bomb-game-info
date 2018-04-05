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

defined( 'ABSPATH' ) or die('Failed.');

require_once('vendor/widget-support/widget-support.php');

add_action('init', 'GiantBombGameInfoMetaBox::register_resources');
add_action('init', 'GiantBombGameInfoWidget::register_resources');

add_action('admin_init', 'GiantBombGameInfoCommon::register_settings');
add_action('admin_menu', 'GiantBombGameInfoAdminMenu::register_admin_menu' );


// Register the widget
add_action('widgets_init', function(){
    register_widget("GiantBombGameInfoWidget");
});

add_action(
    'add_meta_boxes', 
    'GiantBombGameInfoMetaBox::do_add_meta_box'
);


// --------------------------------------------------------------------
// --------------------------------------------------------------------

class GiantBombGameInfoCommon {



}

// --------------------------------------------------------------------
// --------------------------------------------------------------------



// --------------------------------------------------------------------
// --------------------------------------------------------------------

class GiantBombGameInfoMetaBox {

    private static $gbgi_metabox_nonce_name = 'gbgi-metabox-nonce';

    public static function register_resources(){

        GiantBombGameInfoCommon::register_resources();

        wp_register_script(
            'gbgi_admin_metabox', 
            plugins_url('/js/metabox.js', __FILE__)
        );

    }

    public static function do_add_meta_box($post_type) {
        
        //limit meta box to certain post types
        $post_types = array('post', 'podcast', 'video');     
        
        if ( in_array( $post_type, $post_types )) {
        
            add_meta_box(
                'gbinfo-game-id',
                __('NEW Game Info', 'gbinfo'),
                'GiantBombGameInfoMetaBox::create_meta_box',
                null,
                'side'
            );
        
        }

    }

    public static function create_meta_box($post) {

        wp_enqueue_script('gbgi_core');
        wp_enqueue_script('gbgi_admin_metabox');
        wp_enqueue_style( 'gbgi_metabox');

        $apiKey = GiantBombGameInfoCommon::get_api_key();

        $context = [
            'apiKey' => $apiKey
        ];

        echo '<!-- GBGI Meta Box -->';

        Timber::render('templates/metabox.twig', $context);

    }

    public static function save_data() {

        /* Verify the nonce before proceeding. */
        if ( 
            !isset( $_POST['smashing_post_class_nonce'] ) || 
            !wp_verify_nonce( $_POST['smashing_post_class_nonce'], basename( __FILE__ ) ) 
        ){
            return $post_id;
        }
        
        /* Get the post type object. */
        $post_type = get_post_type_object( $post->post_type );

        /* Check if the current user has permission to edit the post. */
        if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

        /* Get the posted data and sanitize it for use as an HTML class. */
        $new_meta_value = ( isset( $_POST['smashing-post-class'] ) ? sanitize_html_class( $_POST['smashing-post-class'] ) : '' );

        /* Get the meta key. */
        $meta_key = 'smashing_post_class';

        /* Get the meta value of the custom field key. */
        $meta_value = get_post_meta( $post_id, $meta_key, true );

        /* If a new meta value was added and there was no previous value, add it. */
        if ( $new_meta_value && '' == $meta_value ){
            add_post_meta( $post_id, $meta_key, $new_meta_value, true );
        }

        /* If the new meta value does not match the old value, update it. */
        elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
            update_post_meta( $post_id, $meta_key, $new_meta_value );
        }
        
        /* If there is no new meta value but an old value exists, delete it. */
        elseif ( '' == $new_meta_value && $meta_value ) {
            delete_post_meta( $post_id, $meta_key, $meta_value );
        }
    }

}

// --------------------------------------------------------------------
// --------------------------------------------------------------------

class GiantBombGameInfoWidget extends \WP_Widget {

    private $widget_path;
    
    private $default_args = [

    ];


    public static function register_resources() {


    }

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {

        $this->widget_path = dirname(__FILE__);
            
        $widget_ops = array(
            'description' => 'Attach game info to a post, pulled from the Giant Bomb API',
        );

        parent::__construct(
            'giant-bomb-game-info-widget', 
            'Giant Bomb Game Info', 
            $widget_ops
        );

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