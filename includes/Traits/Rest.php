<?php
namespace CloudServerManagement\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Rest
 *
 * This trait provides methods to register REST API routes and handle JSON responses in the WordPress plugin.
 *
 * @package 
 */
trait Rest {

	// Namespace for the REST API routes specific to this plugin.
	public $namespace = 'csm/v1';

	/**
	 * Registers a new REST API route.
	 *
	 * @param string $path The route path.
	 * @param array  $args The route arguments.
	 */
	public function register_route( $path, $args ) {

		// If a permission callback is specified in the arguments, set it correctly.
		if ( isset( $args['permission'] ) ) {
			$args['permission_callback'] = $args['permission'];
			unset( $args['permission'] );
		}

		// Register the route with the specified namespace, path, and arguments.
		register_rest_route( $this->namespace, $path, $args );
	}

	/**
	 * Sends a JSON response success message.
	 *
	 * @param mixed $data The data to encode as JSON and send.
	 * @param int   $status_code HTTP status code to send with the response. Default is 200.
	 */
	public function response_success( $data = null, $status_code = 200 ) {
		status_header( $status_code );
		wp_send_json_success( $data );
	}

	/**
	 * Sends a JSON response error message.
	 *
	 * @param mixed $data The data to encode as JSON and send.
	 * @param int   $status_code HTTP status code to send with the response. Default is 400.
	 */
	public function response_error( $data = null, $status_code = 400 ) {
		status_header( $status_code );
		wp_send_json_error( $data );
	}

	/**
	 * Sends a JSON response with arbitrary data.
	 *
	 * @param mixed $data The data to encode as JSON and send.
	 * @param int   $status_code HTTP status code to send with the response. Default is 200.
	 */
	public function response( $data, $status_code = 200 ) {
		status_header( $status_code );
		wp_send_json( $data );
	}

	/**
     * Validate server data before insert/update.
     *
     * @param array $data The server data to validate.
     * @param int|null $id Server ID (if updating, to exclude current record from uniqueness check).
     *
     * @return string|null Error message if validation fails, otherwise null.
     */
    public function validate_server_data( $data, $id = null, $table ) {
    	global $wpdb;

        // ✅ Validate name: required & unique per provider
        if ( empty( $data['name'] ) ) {
            return 'Server name is required.';
        }

        $query = $wpdb->prepare(
            "SELECT id FROM $table WHERE name = %s AND provider = %s" . ( $id ? " AND id != %d" : "" ),
            $id ? [ $data['name'], $data['provider'], $id ] : [ $data['name'], $data['provider'] ]
        );
        $exists = $wpdb->get_var( $query );
        if ( $exists ) {
            return 'Server name must be unique per provider.';
        }

        // ✅ Validate IP: required, valid IPv4, and unique
        if ( empty( $data['ip_address'] ) || ! filter_var( $data['ip_address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            return 'A valid IPv4 address is required.';
        }

        $query = $wpdb->prepare(
            "SELECT id FROM $table WHERE ip_address = %s" . ( $id ? " AND id != %d" : "" ),
            $id ? [ $data['ip_address'], $id ] : [ $data['ip_address'] ]
        );
        $exists = $wpdb->get_var( $query );
        if ( $exists ) {
            return 'IP address must be unique.';
        }

        // ✅ Validate provider: must be one of the allowed values
        $valid_providers = [ 'aws', 'digitalocean', 'vultr', 'other' ];
        if ( empty( $data['provider'] ) || ! in_array( $data['provider'], $valid_providers, true ) ) {
            return 'Invalid provider. Allowed values: aws, digitalocean, vultr, other.';
        }

        // ✅ Validate status: must be one of the allowed values
        $valid_statuses = [ 'active', 'inactive', 'maintenance' ];
        if ( empty( $data['status'] ) || ! in_array( $data['status'], $valid_statuses, true ) ) {
            return 'Invalid status. Allowed values: active, inactive, maintenance.';
        }

        // ✅ Validate resources within sanity ranges
        if ( empty( $data['cpu_cores'] ) || $data['cpu_cores'] < 1 || $data['cpu_cores'] > 128 ) {
            return 'CPU cores must be between 1 and 128.';
        }

        if ( empty( $data['ram_mb'] ) || $data['ram_mb'] < 512 || $data['ram_mb'] > 1048576 ) {
            return 'RAM must be between 512 MB and 1,048,576 MB.';
        }

        if ( empty( $data['storage_gb'] ) || $data['storage_gb'] < 10 || $data['storage_gb'] > 1048576 ) {
            return 'Storage must be between 10 GB and 1,048,576 GB.';
        }

        // ✅ All good, no errors
        return null;
    }
}
