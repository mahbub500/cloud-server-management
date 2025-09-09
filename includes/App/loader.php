<?php
namespace CloudServerManagement\App;
use CloudServerManagement\API\API;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Loader {

    public function run() {
        // API
        $rest = new API();
    }
}
