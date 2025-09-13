<?php
namespace CloudServerManagement\App\Controller;
use CloudServerManagement\API\Api;
use CloudServerManagement\App\Front;
use CloudServerManagement\App\Shortcode;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Loader {

    public function run() {

        if ( ! is_admin() ) {
            $front = new Front();
            $hortcode = new Shortcode();
        }
        // API
        $rest = new Api();
    }

    
}
