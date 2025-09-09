<?php
namespace CloudServerManagement\App;
use CloudServerManagement\API\API;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Loader {

    public function run() {
        // Admin
        if ( is_admin() ) {
            $admin = new Admin();
            $admin->init();
        }

        // Front
        $front = new Front();
        $front->init();

        // API
        $rest = new API();
    }
}
