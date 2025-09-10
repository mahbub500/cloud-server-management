<?php
namespace CloudServerManagement\App\Controller;
use CloudServerManagement\API\API;
use CloudServerManagement\App\Front;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Loader {

    public function run() {

        if ( ! is_admin() ) {
            $front = new Front();
        }
        // API
        $rest = new API();
    }

    
}
