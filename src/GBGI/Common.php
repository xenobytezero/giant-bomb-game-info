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
    private static $OPTION_KEY = "qKUV6yWH2/e/4ofwQNvoE6oSYV+UN3l76wuXhdh22qM=";


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
            'Common::save_option_callback'
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
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode(Common::$OPTION_KEY);
        // Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function dec($data){
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode(Common::$OPTION_KEY);
        // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }
}

?>