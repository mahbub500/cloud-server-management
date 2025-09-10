<?php
namespace CloudServerManagement\App;
use CloudServerManagement\API\API;
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
