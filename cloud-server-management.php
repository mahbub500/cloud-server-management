<?php
/**
 * Plugin Name 	: Cloud Server Management
 * Description	: Clud server management
 * Plugin URI 	: https://techwithmahbub.com/
 * Author		: Mahbub
 * Author URI 	: https://techwithmahbub.com/
 * Version 		: 0.9
 * Text Domain	: cloud-server-management
 * Domain Path	: /languages
 */

namespace CloudServerManagement;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

/**
 * Essential Constants
 */
define( __NAMESPACE__ . '\CSM_VERSION', '1.0.0' );
define( __NAMESPACE__ . '\CSM_PLUGIN_FILE', __FILE__ );
define( __NAMESPACE__ . '\CSM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( __NAMESPACE__ . '\CSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\CSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\CSM_ASSETS_URL', CSM_PLUGIN_URL . 'assets/' );

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
register_activation_hook( __FILE__, array( __NAMESPACE__ . '\CSM_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\CSM_Deactivator', 'deactivate' ) );

/**
 * Initialize Plugin
 */
function run_csm() {
    $plugin = new CSM_Loader();
    $plugin->run();
}
run_csm();