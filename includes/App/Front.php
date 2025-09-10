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
        $this->action( 'wp_head', [ $this,'head' ] );    
        $this->action( 'wp_enqueue_scripts', [ $this,'enqueue_assets' ] );    
    }

    public function head(){
        $plugin_path = plugin_dir_path( __FILE__ );

        // Utility::pri( CSM_ASSETS_URL );
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'csm',
            CSM_ASSETS_URL . 'css/index.css',
            [],
            CSM_VERSION
        );

        wp_enqueue_script(
            'csm', 
            CSM_ASSETS_URL . 'js/index.js',
            ['jquery'], 
            CSM_VERSION,
            true
        );
    }
}
