<?php
namespace CloudServerManagement;

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

        // // REST
        // $rest = new REST();
        // $rest->init();
    }
}
