<?php
namespace CloudServerManagement\App;

defined( 'ABSPATH' ) || exit;

use CloudServerManagement\Traits\Rest;
use WpPluginHub\Plugin\Base;

class API extends Base {

    use Rest;

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->plugin   = $plugin;
        $this->slug     = $this->plugin['TextDomain'];
        $this->name     = $this->plugin['Name'];
        $this->server   = $this->plugin['server'];
        $this->version  = $this->plugin['Version'];
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
}
