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
        $this->shortcode( 'csm_app', [ $this,'csm_app' ] );    
            
    }

    public function csm_app(){
         return '<div id="csm-app"></div>';
    }
}