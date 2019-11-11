<?php
/**
 * Single Match - Box Scores
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$sport = get_option('wpcm_sport');
$sep = get_option('wpcm_match_goals_delimiter');
$intgoals = unserialize( get_post_meta( $post->ID, 'wpcm_goals', true) );
$played = get_post_meta( $post->ID, 'wpcm_played', true );
$home_club = get_post_meta( $post->ID, 'wpcm_home_club', true );
$away_club = get_post_meta( $post->ID, 'wpcm_away_club', true );
$overtime = get_post_meta( $post->ID, 'wpcm_overtime', true );
$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );

$sports = array( 'volleyball', 'basketball', 'football', 'footy', 'hockey', 'floorball' );
if( in_array( $sport, $sports ) ) {

    if ( $played ) { ?>

        <table class="wpcm-ss-table wpcm-box-scores">
            <thead>
                <tr>
                    <th></th>                  
                    <th><?php _e( '1st', 'wp-club-manager' ); ?></th>            
                    <th><?php _e( '2nd', 'wp-club-manager' ); ?></th>      
                    <th><?php _e( '3rd', 'wp-club-manager' ); ?></th>
                    <?php
                    $sports = array( 'volleyball', 'basketball', 'football', 'footy' );
                    if( in_array( $sport, $sports ) ) { ?>
                        <th><?php _e( '4th', 'wp-club-manager' ); ?></th>
                    <?php }
                    if( $sport == 'volleyball' ) { ?>
                        <th><?php _e( '5th', 'wp-club-manager' ); ?></th>
                    <?php }
                    if( $sport == 'hockey' || $sport == 'floorball' ) {
                        if ( $overtime == '1' ) { ?>
                            <th><?php _ex( 'OT', 'Overtime', 'wp-club-manager' ); ?></th>
                        <?php }
                        if ( $shootout == '1' ) { ?>
                            <th><?php _ex( 'SO', 'Shootout', 'wp-club-manager' ); ?></th>
                        <?php }
                    } ?>
                    <th><?php _ex( 'T', 'Total', 'wp-club-manager' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo get_the_title( $home_club ); ?></td>
                    <?php
                    if ( isset( $intgoals['q1'] ) ) { ?>
                        <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q1']['home'] ); ?></td>
                    <?php }
                    if ( isset( $intgoals['q2'] ) ) { ?>
                        <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q2']['home'] ); ?></td>
                    <?php }
                    if ( isset( $intgoals['q3'] ) ) { ?>
                        <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q3']['home'] ); ?></td>
                    <?php }
                    if( in_array( $sport, $sports ) ) {
                        if ( isset( $intgoals['q4'] ) ) { ?>
                            <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q4']['home'] ); ?></td>
                        <?php }
                    }
                    if( $sport == 'volleyball' ) {
                        if ( isset( $intgoals['q5'] ) ) { ?>
                            <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q5']['home'] ); ?></td>
                        <?php }
                    }
                    if( $sport == 'hockey' || $sport == 'floorball' ) {
                        if ( $overtime == '1' ) {
                            if ( $intgoals['total']['home'] > $intgoals['total']['away'] ) { ?>
                                <td><?php _e( '1', 'wp-club-manager' ); ?></td>
                            <?php } else { ?>
                                <td><?php _e( '0', 'wp-club-manager' ); ?></td>
                            <?php }
                        }
                        if ( $shootout == '1' ) {
                            if ( $intgoals['total']['home'] > $intgoals['total']['away'] ) { ?>
                                <td><?php _e( '1', 'wp-club-manager' ); ?></td>
                            <?php } else { ?>
                                <td><?php _e( '0', 'wp-club-manager' ); ?></td>
                            <?php }
                        }
                    } ?>
                    <td><?php echo $intgoals['total']['home']; ?></td>
                </tr>
                <tr>
                    <td><?php echo get_the_title( $away_club ); ?></td>
                    <?php                   
                    if ( isset( $intgoals['q1'] ) ) { ?>
                        <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q1']['away'] ); ?></td>
                    <?php }
                    if ( isset( $intgoals['q2'] ) ) { ?>
                        <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q2']['away'] ); ?></td>
                    <?php }
                    if ( isset( $intgoals['q3'] ) ) { ?>
                        <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q3']['away'] ); ?></td>
                    <?php }
                    if( in_array( $sport, $sports ) ) {
                        if ( isset( $intgoals['q4'] ) ) { ?>
                            <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q4']['away'] ); ?></td>
                        <?php }
                    }
                    if( $sport == 'volleyball' ) {
                        if ( isset( $intgoals['q5'] ) ) { ?>
                            <td><?php echo ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ? _e( 'x', 'wp-club-manager' ) : $intgoals['q5']['away'] ); ?></td>
                        <?php }
                    }
                    if( $sport == 'hockey' || $sport == 'floorball' ) {
                        if ( $overtime == '1' ) {
                            if ( $intgoals['total']['away'] > $intgoals['total']['home'] ) { ?>
                                <td>1</td>
                            <?php } else { ?>
                                <td>0</td>
                            <?php }
                        }
                        if ( $shootout == '1' ) {
                            if ( $intgoals['total']['away'] > $intgoals['total']['home'] ) { ?>
                                <td>1</td>
                            <?php } else { ?>
                                <td>0</td>
                            <?php }
                        }
                    } ?>
                    <td><?php echo $intgoals['total']['away']; ?></td>
                </tr>
            </tbody>
        </table>
    <?php
    }
} else {
    if ( $played ) {
        if ( isset( $intgoals['q1'] ) ) { ?>
            <div class="wpcm-ss-halftime wpcm-box-scores">
                <?php if( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() ) {
                    _ex( 'HT:', 'Half time', 'wp-club-manager' ); ?> <?php _e( 'x', 'wp-club-manager' ); ?> <?php echo $sep; ?> <?php _e( 'x', 'wp-club-manager' );
                } else {
                    _ex( 'HT:', 'Half time', 'wp-club-manager' ); ?> <?php echo $intgoals['q1']['home']; ?> <?php echo $sep; ?> <?php echo $intgoals['q1']['away'];
                } ?>
            </div>
        <?php }
    }
}