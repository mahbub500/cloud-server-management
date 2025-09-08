<?php
/**
 * All admin facing functions
 */
namespace WpPluginHub\Cloud_Server_Management\App;
use WpPluginHub\Plugin\Base;
use WpPluginHub\Plugin\Metabox;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Admin
 * @author WpPluginHub <mahbubmr500@gmail.com>
 */
class Admin extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
	}

	/**
	 * Internationalization
	 */
	public function i18n() {
		load_plugin_textdomain( 'cloud-server-management', false, CLOUD_SERVER_MANAGEMENT_DIR . '/languages/' );
	}

	/**
	 * Installer. Runs once when the plugin in activated.
	 *
	 * @since 1.0
	 */
	public function install() {

		if( ! get_option( 'cloud-server-management_version' ) ){
			update_option( 'cloud-server-management_version', $this->version );
		}
		
		if( ! get_option( 'cloud-server-management_install_time' ) ){
			update_option( 'cloud-server-management_install_time', time() );
		}
	}

	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'CLOUD_SERVER_MANAGEMENT_DEBUG' ) && CLOUD_SERVER_MANAGEMENT_DEBUG ? '' : '.min';
		
		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/admin{$min}.css", CLOUD_SERVER_MANAGEMENT ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/admin{$min}.js", CLOUD_SERVER_MANAGEMENT ), [ 'jquery' ], $this->version, true );
	}

	public function footer_text( $text ) {
		if( get_current_screen()->parent_base != $this->slug ) return $text;

		return sprintf( __( 'Built with %1$s by the folks at <a href="%2$s" target="_blank">Codexpert, Inc</a>.' ), '&hearts;', 'https://codexpert.io' );
	}

	public function modal() {
		echo '
		<div id="cloud-server-management-modal" style="display: none">
			<img id="cloud-server-management-modal-loader" src="' . esc_attr( CLOUD_SERVER_MANAGEMENT_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}
}