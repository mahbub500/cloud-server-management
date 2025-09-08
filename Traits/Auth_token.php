<?php
class Auth_Token {

    /**
     * Generate a new token for a user after login.
     *
     * @param int $user_id The user ID.
     * @return string The generated token.
     */
    public function generate_token( $user_id ) {
        // Generate a random string
        $token = wp_generate_password( 32, false, false );

        // Save token in user meta with an expiration (optional: 1 day)
        update_user_meta( $user_id, '_auth_token', $token );
        update_user_meta( $user_id, '_auth_token_expiry', time() + DAY_IN_SECONDS );

        return $token;
    }

    /**
     * Validate token for a request.
     *
     * @param string $token The token to validate.
     * @return int|false User ID if valid, false otherwise.
     */
    public function validate_token( $token ) {
        $users = get_users( [
            'meta_key'   => '_auth_token',
            'meta_value' => $token,
            'number'     => 1,
            'fields'     => 'ID',
        ] );

        if ( empty( $users ) ) {
            return false;
        }

        $user_id = $users[0];
        $expiry  = get_user_meta( $user_id, '_auth_token_expiry', true );

        // Check expiry
        if ( $expiry && time() > $expiry ) {
            delete_user_meta( $user_id, '_auth_token' );
            delete_user_meta( $user_id, '_auth_token_expiry' );
            return false;
        }

        return $user_id;
    }
}
