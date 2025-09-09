<?php
namespace CloudServerManagement\App;


/**
 * Database class to handle plugin tables
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Database {

    public $plugin;

    public function __construct( $plugin ) {
        $this->plugin    = $plugin;
        $this->slug      = $this->plugin['TextDomain'];
        $this->name      = $this->plugin['Name'];
        $this->version   = $this->plugin['Version'];
    }

    /**
     * Create the csm_servers table
     */
    public function create_servers_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'csm_servers';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            provider ENUM('aws','digitalocean','vultr','other') NOT NULL,
            status ENUM('active','inactive','maintenance') NOT NULL DEFAULT 'active',
            cpu_cores INT UNSIGNED NOT NULL,
            ram_mb BIGINT UNSIGNED NOT NULL,
            storage_gb BIGINT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY unique_name_provider (name, provider),
            UNIQUE KEY unique_ip (ip_address)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
