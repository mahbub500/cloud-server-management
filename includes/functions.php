<?php
if( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * Gets the site's base URL
 * 
 * @uses get_bloginfo()
 * 
 * @return string $url the site URL
 */
if( ! function_exists( 'csm_site_url' ) ) :
function csm_site_url() {
	$url = get_bloginfo( 'url' );

	return $url;
}
endif;

if ( ! function_exists( 'csm_insert' ) ) {
	function csm_insert() {
	    global $wpdb;

	    $table = $wpdb->prefix . 'csm_servers';

	    $sql = "
	    INSERT INTO $table (name, ip_address, provider, status, cpu_cores, ram_mb, storage_gb)
	    VALUES
	    ('Web Server 1', '192.168.1.10', 'aws', 'active', 2, 2048, 50),
	    ('Web Server 2', '192.168.1.11', 'aws', 'inactive', 4, 4096, 100),
	    ('Database Server 1', '192.168.1.12', 'digitalocean', 'active', 8, 16384, 500),
	    ('App Server 1', '192.168.1.13', 'vultr', 'maintenance', 2, 2048, 80),
	    ('App Server 2', '192.168.1.14', 'other', 'active', 4, 8192, 120),
	    ('Cache Server 1', '192.168.1.15', 'aws', 'active', 2, 1024, 40),
	    ('Backup Server 1', '192.168.1.16', 'digitalocean', 'inactive', 4, 8192, 200),
	    ('Load Balancer 1', '192.168.1.17', 'vultr', 'active', 2, 2048, 30),
	    ('Monitoring Server 1', '192.168.1.18', 'aws', 'active', 2, 2048, 60),
	    ('File Server 1', '192.168.1.19', 'other', 'active', 6, 16384, 1000),
	    ('File Server 2', '192.168.1.20', 'aws', 'inactive', 8, 32768, 2000),
	    ('Game Server 1', '192.168.1.21', 'digitalocean', 'active', 4, 8192, 500),
	    ('Proxy Server 1', '192.168.1.22', 'vultr', 'active', 2, 2048, 50),
	    ('Mail Server 1', '192.168.1.23', 'aws', 'maintenance', 4, 8192, 200),
	    ('DNS Server 1', '192.168.1.24', 'digitalocean', 'active', 2, 2048, 50),
	    ('Web Server 3', '192.168.1.25', 'vultr', 'active', 2, 4096, 100),
	    ('Database Server 2', '192.168.1.26', 'aws', 'active', 8, 32768, 800),
	    ('App Server 3', '192.168.1.27', 'digitalocean', 'inactive', 4, 8192, 150),
	    ('Cache Server 2', '192.168.1.28', 'other', 'active', 2, 4096, 70),
	    ('Load Balancer 2', '192.168.1.29', 'aws', 'active', 2, 2048, 40)
	    ";

	    $wpdb->query($sql);

	    return "✅ 20 sample servers inserted successfully.";
	}

}