<?php
namespace CloudServerManagement\App\Controller;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Activator {

    /**
     * Fired during plugin activation
     */
    public static function activate() {
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
