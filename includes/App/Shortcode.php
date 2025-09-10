<?php
namespace CloudServerManagement\App;
use CloudServerManagement\Helper\Utility;
use CloudServerManagement\Traits\Hook;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Shortcode {
    use Hook;

    public function __construct() {
        $this->action( 'wp_head', [ $this,'head' ] );    
            
    }

    public function head(){
        
    }
}