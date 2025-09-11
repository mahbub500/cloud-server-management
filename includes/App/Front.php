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

        // Utility::pri( CSM_PLUGIN_URL );
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
            [ 'wp-element' ], 
            CSM_VERSION,
            true
        );

        wp_enqueue_script(
            'csm-react', 
            CSM_PLUGIN_URL . 'build/main.js',
            [ 'wp-element' ], 
            CSM_VERSION,
            true
        );
        $localized = [
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
            'root'  => esc_url_raw( rest_url( 'csm/v1' ) ),
            '_wpnonce' => wp_create_nonce( 'wp_rest' ),
        ];
        wp_localize_script( 'csm', 'CSM_API', $localized );
    }
}
