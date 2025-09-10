<?php
namespace CloudServerManagement\App;
use CloudServerManagement\Helper\Utility;
use CloudServerManagement\Traits\Hook;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Front {

    use Hook;

    public function __construct() {
        $this->action( 'wp_head', 'head' );
    }

    public function head(){
        Utility::pri( 'Hi' );
    }
}
