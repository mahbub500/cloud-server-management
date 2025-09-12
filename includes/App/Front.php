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

        csm_insert();

        // Utility::pri( CSM_PLUGIN_URL );
        // Utility::pri( CSM_ASSETS_URL );
    }

    public function enqueue_assets() {
        // Plugin/Theme Styles
        wp_enqueue_style(
            'csm',
            CSM_ASSETS_URL . 'css/index.css',
            [],
            CSM_VERSION
        );

        // Bootstrap CSS
        wp_enqueue_style(
            'csm-bootstrap-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css',
            [],
            '4.6.2'
        );

        // Your main script (React/JS)
        wp_enqueue_script(
            'csm', 
            CSM_ASSETS_URL . 'js/index.js',
            [ 'wp-element' ], 
            CSM_VERSION,
            true
        );

        // Bootstrap JS (with Popper, requires jQuery)
        wp_enqueue_script(
            'csm-bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
            [ 'jquery' ], 
            '4.6.2',
            true
        );


        wp_enqueue_script(
            'csm-react', 
            CSM_PLUGIN_URL . 'build/main.js',
            [ 'wp-element' ], 
            time(),
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
