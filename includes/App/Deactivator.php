<?php
namespace CloudServerManagement;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Deactivator {

    /**
     * Fired during plugin deactivation
     */
    public static function deactivate() {
        // Optional: clear scheduled events, flush rewrite rules, etc.
        flush_rewrite_rules();
    }
}
