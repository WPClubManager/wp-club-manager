<?php
/**
 * Feedback functions
 *
 * @package     WPClubManager
 * @subpackage  Admin/Feedback
 * @copyright   Copyright (c) 2017, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.6
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * display deactivation logic on plugins page
 * 
 * @since 1.5.6
 */
function wpcm_add_deactivation_feedback_modal() {

    $screen = get_current_screen();
    if( !is_admin() && !is_plugins_page()) {
        return;
    }

    $current_user = wp_get_current_user();
    if( !($current_user instanceof WP_User) ) {
        $email = '';
    } else {
        $email = trim( $current_user->user_email );
    }

    include 'views/html-admin-deactivate-feedback.php';
}

