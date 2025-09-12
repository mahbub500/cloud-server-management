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

// INSERT INTO wp_csm_servers(name, ip_address, provider, status, cpu_cores, ram_mb, storage_gb)
// VALUES
// ('Web Server 1', '192.168.1.10', 'aws', 'active', 2, 2048, 50),
// ('Web Server 2', '192.168.1.11', 'aws', 'inactive', 4, 4096, 100),
// ('Database Server 1', '192.168.1.12', 'digitalocean', 'active', 8, 16384, 500),
// ('App Server 1', '192.168.1.13', 'vultr', 'maintenance', 2, 2048, 80),
// ('App Server 2', '192.168.1.14', 'other', 'active', 4, 8192, 120),
// ('Cache Server 1', '192.168.1.15', 'aws', 'active', 2, 1024, 40),
// ('Backup Server 1', '192.168.1.16', 'digitalocean', 'inactive', 4, 8192, 200),
// ('Load Balancer 1', '192.168.1.17', 'vultr', 'active', 2, 2048, 30),
// ('Monitoring Server 1', '192.168.1.18', 'aws', 'active', 2, 2048, 60),
// ('File Server 1', '192.168.1.19', 'other', 'active', 6, 16384, 1000),
// ('File Server 2', '192.168.1.20', 'aws', 'inactive', 8, 32768, 2000),
// ('Game Server 1', '192.168.1.21', 'digitalocean', 'active', 4, 8192, 500),
// ('Proxy Server 1', '192.168.1.22', 'vultr', 'active', 2, 2048, 50),
// ('Mail Server 1', '192.168.1.23', 'aws', 'maintenance', 4, 8192, 200),
// ('DNS Server 1', '192.168.1.24', 'digitalocean', 'active', 2, 2048, 50),
// ('Web Server 3', '192.168.1.25', 'vultr', 'active', 2, 4096, 100),
// ('Database Server 2', '192.168.1.26', 'aws', 'active', 8, 32768, 800),
// ('App Server 3', '192.168.1.27', 'digitalocean', 'inactive', 4, 8192, 150),
// ('Cache Server 2', '192.168.1.28', 'other', 'active', 2, 4096, 70),
// ('Load Balancer 2', '192.168.1.29', 'aws', 'active', 2, 2048, 40);