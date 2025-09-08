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
     * Register REST API routes
     */
    public function register_routes() {

        // Route: /servermanager/v1/signup
        $this->register_route( '/signup', [
            'methods'             => 'POST',
            'callback'            => [$this, 'signup_user'],
            'permission'          => '__return_true', // public route
            'args'                => [
                'email'    => [
                    'required' => true,
                    'type'     => 'string',
                ],
                'password' => [
                    'required' => true,
                    'type'     => 'string',
                ],
            ],
        ] );
    }

    /**
     * Handle user signup
     */
    public function signup_user( $request ) {
        $email    = sanitize_email( $request->get_param('email') );
        $password = $request->get_param('password');

        if ( ! is_email( $email ) ) {
            return $this->response_error( 'Invalid email address.' );
        }

        if ( empty( $password ) || strlen( $password ) < 6 ) {
            return $this->response_error( 'Password must be at least 6 characters.' );
        }

        if ( email_exists( $email ) ) {
            return $this->response_error( 'Email already registered.' );
        }

        // Generate username from email
        $username = sanitize_user( current( explode( '@', $email ) ), true );

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            return $this->response_error( $user_id->get_error_message() );
        }

        return $this->response_success([
            'user_id' => $user_id,
            'email'   => $email,
            'username'=> $username
        ], 201);
    }
}
