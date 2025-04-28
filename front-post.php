<?php

/**
 * Front Post
 * @package           Front Post
 * @author            WPCat
 * Plugin Name:       Front Post
 * Description:       Creating posts in frontend
 * Version:           1.0
 */

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
define('FP_DIR', plugin_dir_path(__FILE__));
define('FP_URI', plugin_dir_url(__FILE__));

require FP_DIR . 'includes\class-fp-core.php';

// error_log(print_r(FP_DIR, 1));
// error_log(print_r(FP_URI, 1));

function fp(){
    return FP_Core::instance();
}

fp();