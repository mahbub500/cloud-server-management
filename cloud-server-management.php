<?php
/**
 * Plugin Name: Cloud Server Management
 * Description: Cloud server management
 * Plugin URI: https://techwithmahbub.com/
 * Author: Mahbub
 * Author URI: https://techwithmahbub.com/
 * Version: 0.9
 * Text Domain: cloud-server-management
 * Domain Path: /languages
 */

namespace CloudServerManagement;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

/**
 * Essential Constants
 */
define( 'CSM_VERSION', '1.0.0' );
define( 'CSM_PLUGIN_FILE', __FILE__ );
define( 'CSM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CSM_PLUGIN_NAME', 'cloud-server-management' );
define( 'CSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CSM_ASSETS_URL', CSM_PLUGIN_URL . 'assets/' );

/**
 * Autoload (Composer)
 */
if ( file_exists( CSM_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once CSM_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * Functions file
 */
require_once CSM_PLUGIN_DIR . 'includes/functions.php';

/**
 * Activation / Deactivation Hooks
 */
register_activation_hook( __FILE__, array( __NAMESPACE__ . '\App\Controller\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\App\Controller\Deactivator', 'deactivate' ) );

/**
 * Run plugin when all plugins are loaded
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\\csm_active' );

function csm_active() {
    $plugin = new \CloudServerManagement\App\Controller\Loader();
    $plugin->run();
}