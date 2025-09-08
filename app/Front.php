<?php
/**
 * All public facing functions
 */
namespace WpPluginHub\Cloud_Server_Management\App;
use WpPluginHub\Plugin\Base;
use WpPluginHub\Cloud_Server_Management\Helper;
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Front
 * @author WpPluginHub <mahbubmr500@gmail.com>
 */
class Front extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];
	}

	public function head() {}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'CLOUD_SERVER_MANAGEMENT_DEBUG' ) && CLOUD_SERVER_MANAGEMENT_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/front{$min}.css", CLOUD_SERVER_MANAGEMENT ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/front{$min}.js", CLOUD_SERVER_MANAGEMENT ), [ 'jquery' ], $this->version, true );
		
		$localized = [
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'_wpnonce'	=> wp_create_nonce(),
		];
		wp_localize_script( $this->slug, 'CLOUD_SERVER_MANAGEMENT', apply_filters( "{$this->slug}-localized", $localized ) );
	}

	public function modal() {
		echo '
		<div id="cloud-server-management-modal" style="display: none">
			<img id="cloud-server-management-modal-loader" src="' . esc_attr( CLOUD_SERVER_MANAGEMENT_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}
}