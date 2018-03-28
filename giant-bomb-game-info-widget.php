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

    public static $OPTION_GROUP = 'gbgi';
    public static $OPTION_NAME = 'gbgi_opts';

    private static $OPTION_KEY = "qKUV6yWH2/e/4ofwQNvoE6oSYV+UN3l76wuXhdh22qM=";

    public static function register_resources() {

        wp_register_script(
            'gbgi_core',
            plugins_url('/js/gbgi_core.js', __FILE__)
        );

    }

    public static function register_settings() {
        register_setting(
            GiantBombGameInfoCommon::$OPTION_GROUP, 
            GiantBombGameInfoCommon::$OPTION_NAME,
            'GiantBombGameInfoCommon::save_option_callback'
        );
    }

    public static function save_option_callback($input) {

        if (isset($_POST['reset'])) {
            $input['apiKey'] = "";
            add_settings_error(
                'gbgi', 
                'api-reset', 
                'The API Key was reset', 
                'updated' 
            );
        } else {
            $input['apiKey'] = GiantBombGameInfoCommon::enc($input['apiKey']);
        }

        return $input;
    }

    public static function get_api_key() {
        $options = get_option(GiantBombGameInfoCommon::$OPTION_NAME);
        $api_key = GiantBombGameInfoCommon::dec($options['apiKey']);
        return $api_key;
    }

    public static function enc($data){
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode(GiantBombGameInfoCommon::$OPTION_KEY);
        // Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function dec($data){
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode(GiantBombGameInfoCommon::$OPTION_KEY);
        // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

}

// --------------------------------------------------------------------
// --------------------------------------------------------------------

class GiantBombGameInfoAdminMenu {

    public static function register_admin_menu() {
        add_options_page(
            'Giant Bomb Game Info',
            'Giant Bomb Game Info',
            'manage_options',
            'giant-bomb-game-info',
            'GiantBombGameInfoAdminMenu::create_admin_menu'
        );
    }

    public static function create_admin_menu() {

        $context = [
            'option_group' => GiantBombGameInfoCommon::$OPTION_GROUP,
            'option_name' => GiantBombGameInfoCommon::$OPTION_NAME,
            'options' => get_option(GiantBombGameInfoCommon::$OPTION_NAME)
        ];

        Timber::render('templates/admin_menu.twig', $context);
    }

}

// --------------------------------------------------------------------
// --------------------------------------------------------------------

class GiantBombGameInfoMetaBox {


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

        $apiKey = GiantBombGameInfoCommon::get_api_key();

        $context = [
            'apiKey' => $apiKey
        ];

        Timber::render('templates/metabox.twig', $context);

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