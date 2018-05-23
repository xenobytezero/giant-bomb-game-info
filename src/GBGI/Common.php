<?php

namespace GBGI;
require_gbgi_autoloader();

// -----------------------------------------------------------------

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

class Common {

    public static $OPTION_GROUP = 'gbgi';
    public static $OPTION_NAME = 'gbgi_opts';
    public static $META_KEY = 'gbgi-gameinfo';

    private static $ENC_FILE_NAME = "enc_key.php";
    private static $PLUGIN_BASE_DIR_CACHE = null;
    private static $PLUGIN_BASE_URL_CACHE = null;


    public static function register_scripts() {
        
        wp_register_script(
            'gbgi_options_page',
            Common::plugin_url('dist/js/options.js') 
        );
    }

    public static function create_encryption_key() {

        $path = Common::plugin_file(Common::$ENC_FILE_NAME);

        //create the file
        $enc_file = fopen($path, "w") or die("Unable to create enc file");

        // write the header
        fwrite($enc_file, "<?php defined( 'ABSPATH' ) or die;");
    
        // write the key
        $key = base64_encode(openssl_random_pseudo_bytes(32));
        fwrite($enc_file, "define('GBGI_ENC_KEY', '" . $key . "');");

        // write the footer
        fwrite($enc_file, "?>");

        // close the file
        fclose($enc_file);

    }

    public static function init_encryption_key() {
        // get the encryption file path
        $path = Common::plugin_file(Common::$ENC_FILE_NAME);
        // include the enc file
        require_once($path);
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
            Common::$OPTION_NAME,
            [
                'sanitize_callback' => ['GBGI\Common', 'save_option_callback']
            ]
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
            $input['apiKey'] = Common::enc($input['apiKey']);
        }

        return $input;
    }

    public static function get_api_key() {
        $options = get_option(Common::$OPTION_NAME);
        $api_key = Common::dec($options['apiKey']);
        return $api_key;
    }

    public static function enc($data){
        
        // Initialise the key if required
        Common::init_encryption_key();
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode(GBGI_ENC_KEY);
        // Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function dec($data){

        // Initialise the key if required
        Common::init_encryption_key();
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode(GBGI_ENC_KEY);
        // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }
}

?>