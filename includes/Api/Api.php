<?php
namespace CloudServerManagement\Api;

defined( 'ABSPATH' ) || exit;

use CloudServerManagement\Traits\Rest;
use CloudServerManagement\Traits\Hook;


class API {

    use Rest;
    use Hook;

    /**
     * Database table name
     * @var string
     */
    protected $table;

    /**
     * Constructor function
     */
    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'csm_servers';

        $this->action( 'rest_api_init', [ $this,'register_routes' ] );
    }

    /**
     * Register REST API routes for user operations.
     *
     * - /signup: Create a new user account.
     * - /login: Authenticate an existing user.
     */
    public function register_routes() {

        // Signup route
        $this->register_route( '/signup', [
            'methods'    => 'POST',
            'callback'   => [$this, 'signup_user'],
            'permission' => '__return_true', // Public route, no authentication required
            'args'       => [
                'email'    => [
                    'required' => true,
                    'type'     => 'string',
                    'sanitize_callback' => 'sanitize_email',
                ],
                'password' => [
                    'required' => true,
                    'type'     => 'string',
                ],
            ],
        ] );

        // Login route
        $this->register_route( '/login', [
            'methods'    => 'POST',
            'callback'   => [$this, 'login_user'],
            'permission' => '__return_true', // Public route, no authentication required
            'args'       => [
                'email'    => [
                    'required' => true,
                    'type'     => 'string',
                    'sanitize_callback' => 'sanitize_email',
                ],
                'password' => [
                    'required' => true,
                    'type'     => 'string',
                ],
            ],
        ] );

        /**
         * Register all CRUD REST API routes.
         *
         * Routes:
         * - GET    /servers           List servers (supports filter, search, sort, pagination)
         * - POST   /servers           Create a new server
         * - GET    /servers/<id>      Get details of a single server
         * - PUT    /servers/<id>      Update an existing server
         * - DELETE /servers/<id>      Delete a server
         */
        // List servers
        $this->register_route( '/servers', [
            'methods'    => 'GET',
            'callback'   => [$this, 'list_servers'],
            'permission' => '__return_true'
        ] );

        // Create a new server
        $this->register_route( '/servers', [
            'methods'    => 'POST',
            'callback'   => [$this, 'create_server'],
            'permission' => '__return_true',
            'args'       => [
                'name'       => ['required' => true, 'type' => 'string'],
                'ip_address' => ['required' => true, 'type' => 'string'],
                'provider'   => ['required' => true, 'type' => 'string'],
                'status'     => ['required' => true, 'type' => 'string'],
                'cpu_cores'  => ['required' => true, 'type' => 'integer'],
                'ram_mb'     => ['required' => true, 'type' => 'integer'],
                'storage_gb' => ['required' => true, 'type' => 'integer'],
            ]
        ] );

        // Get a single server
        $this->register_route( '/servers/(?P<id>\d+)', [
            'methods'    => 'GET',
            'callback'   => [$this, 'get_server'],
            'permission' => '__return_true'
        ] );

        // Update a server
        $this->register_route( '/servers/(?P<id>\d+)', [
            'methods'    => 'PUT',
            'callback'   => [$this, 'update_server'],
            'permission' => '__return_true'
        ] );

        // Delete a server
        $this->register_route( '/servers/(?P<id>\d+)', [
            'methods'    => 'DELETE',
            'callback'   => [$this, 'delete_server'],
            'permission' => '__return_true'
        ] );
    }

     /**
     * Handle user signup.
     *
     * @param \WP_REST_Request $request REST request containing email and password.
     * @return \WP_REST_Response JSON response with user info or error.
     */
    public function signup_user( $request ) {
        $email    = $request->get_param('email');
        $password = $request->get_param('password');

        $email = sanitize_email( $email );

        // Validate email
        if ( ! is_email( $email ) ) {
            return $this->response_error( 'Invalid email address.' );
        }

        // Validate password
        if ( empty( $password ) || strlen( $password ) < 6 ) {
            return $this->response_error( 'Password must be at least 6 characters.' );
        }

        // Check if email is already registered
        if ( email_exists( $email ) ) {
            return $this->response_error( 'Email already registered.' );
        }

        // Generate username from email
        $username = sanitize_user( current( explode( '@', $email ) ), true );

        // Create the user
        $user_id = wp_create_user( $username, $password, $email );

        // Handle errors
        if ( is_wp_error( $user_id ) ) {
            return $this->response_error( $user_id->get_error_message() );
        }

        // Success response
        return $this->response_success([
            'user_id'  => $user_id,
            'username' => $username,
            'email'    => $email
        ], 201);
    }

    /**
     * Handle user login.
     *
     * @param \WP_REST_Request $request REST request containing email and password.
     * @return \WP_REST_Response JSON response with user info or error.
     */
    public function login_user( $request ) {

        $email    = $request->get_param('email');
        $password = $request->get_param('password');

        $email = sanitize_email( $email );

        // Validate email
        if ( ! is_email( $email ) ) {
            return $this->response_error( 'Invalid email address.' );
        }

        // Get user by email
        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            return $this->response_error( 'No user found with this email.' );
        }

        // Check password
        if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
            return $this->response_error( 'Incorrect password.' );
        }

        // Success response
        return $this->response_success([
            'user_id'  => $user->ID,
            'username' => $user->user_login,
            'email'    => $user->user_email
        ], 200);
    }

    /**
     * List servers with optional filter, search, sort, and pagination.
     *
     * Query parameters:
     * - provider: filter by provider (aws|digitalocean|vultr|other)
     * - status: filter by status (active|inactive|maintenance)
     * - search: search by server name or IP
     * - page: pagination page number
     * - per_page: items per page (max 100)
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function list_servers( $request ) {
        global $wpdb;

        $page = max(1, (int) ($request->get_param('page') ?? 1));
        $per_page = min(100, (int) ($request->get_param('per_page') ?? 10));
        $offset = ($page - 1) * $per_page;

        $where = [];
        $args = [];

        // Filter by provider
        if ($provider = $request->get_param('provider')) {
            $where[] = 'provider = %s';
            $args[] = $provider;
        }

        // Filter by status
        if ($status = $request->get_param('status')) {
            $where[] = 'status = %s';
            $args[] = $status;
        }

        // Search by name or IP
        if ($search = $request->get_param('search')) {
            $where[] = '(name LIKE %s OR ip_address LIKE %s)';
            $args[] = '%' . $wpdb->esc_like($search) . '%';
            $args[] = '%' . $wpdb->esc_like($search) . '%';
        }

        $sql = "SELECT * FROM {$this->table}";
        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $args[] = $per_page;
        $args[] = $offset;

        $servers = $wpdb->get_results( $wpdb->prepare($sql, ...$args) );

        return $this->response_success( $servers );
    }

    /**
     * Retrieve details of a single server by ID.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_server( $request ) {
        global $wpdb;
        $id = (int) $request['id'];

        $server = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id) );
        if (!$server) return $this->response_error('Server not found.');

        return $this->response_success($server);
    }

    /**
     * Create a new server entry.
     *
     * Enforces:
     * - Unique name per provider
     * - Unique valid IP address
     * - Resource sanity checks
     * - Valid provider and status values
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function create_server( $request ) {
            global $wpdb;
            // Get request data
            $data = $request->get_params();
            $table = $this->table;

            // Validate server data before insert
            $error = $this->validate_server_data( $data, null, $table );
            if ( $error ) {
                return $this->response_error( $error );
            }

            // Insert into database
            $inserted = $wpdb->insert(
                $table,
                [
                    'name'       => sanitize_text_field( $data['name'] ),
                    'ip_address' => sanitize_text_field( $data['ip_address'] ),
                    'provider'   => sanitize_text_field( $data['provider'] ),
                    'status'     => sanitize_text_field( $data['status'] ),
                    'cpu_cores'  => intval( $data['cpu_cores'] ),
                    'ram_mb'     => intval( $data['ram_mb'] ),
                    'storage_gb' => intval( $data['storage_gb'] ),
                    'created_at' => current_time( 'mysql' ),
                    'updated_at' => current_time( 'mysql' ),
                ]
            );

            // If insert failed
            if ( ! $inserted ) {
                return $this->response_error( 'Failed to create server.' );
            }

            // Return success with inserted server ID
            return $this->response_success( [ 'id' => $wpdb->insert_id ], 201 );
        }


    /**
     * Update an existing server by ID.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function update_server( $request ) {
        global $wpdb;
        $id   = (int) $request['id'];
        $data = $request->get_params();

        if ( empty( $id ) ) {
            return $this->response_error('Server ID is required.');
        }

        // ✅ Check if server exists
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE id = %d",
            $id
        ) );

        if ( ! $exists ) {
            return $this->response_error('Server not found.');
        }

        // Validate only provided fields
        $error = $this->validate_server_data( $data, $id, $this->table, true );
        if ( $error ) return $this->response_error( $error );

        // Build update data dynamically
        $allowed_fields = [ 'name', 'ip_address', 'provider', 'status', 'cpu_cores', 'ram_mb', 'storage_gb' ];
        $update_data    = [];

        foreach ( $allowed_fields as $field ) {
            if ( array_key_exists( $field, $data ) ) {
                $update_data[ $field ] = $data[ $field ];
            }
        }

        // Always update timestamp
        $update_data['updated_at'] = current_time('mysql');

        if ( empty( $update_data ) ) {
            return $this->response_error('No valid fields provided to update.');
        }

        $updated = $wpdb->update( $this->table, $update_data, [ 'id' => $id ] );

        if ( $updated === false ) {
            return $this->response_error('Failed to update server.');
        }

        return $this->response_success([
            'id'             => $id,
            'updated_fields' => array_keys($update_data),
        ]);
    }

    /**
     * Delete a server by ID.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function delete_server( $request ) {
        global $wpdb;
        $id = (int) $request['id'];

        $deleted = $wpdb->delete($this->table, ['id' => $id]);
        if (!$deleted) return $this->response_error('Failed to delete server or server not found.');

        return $this->response_success(['id' => $id]);
    }
}
