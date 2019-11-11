<?php
/**
 * Roster Create
 *
 * Add players to roster.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Roster_Players {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
        
        wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );
        
        $seasons = get_the_terms( $post->ID, 'wpcm_season' );
        $season = $seasons[0]->term_id;
        $teams = get_the_terms( $post->ID, 'wpcm_team' );
        $team = $teams[0]->term_id;
        $players = unserialize( get_post_meta( $post->ID, '_wpcm_roster_players', true ) );
        
        if( empty( $players ) ) {
            
            $args = array(
                'post_type' => 'wpcm_player',
                'posts_per_page' => -1,
                'tax_query' => array()
            );
            if( $season ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'wpcm_season',
                    'field' => 'term_id',
                    'terms' => $season
                );
            }

            if( $team ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'wpcm_team',
                    'field' => 'term_id',
                    'terms' => $team
                );
            }

            $players = get_posts( $args );

        } else {
            
            $args = array(
                'post_type' => 'wpcm_player',
                'posts_per_page' => -1,
                'post__in' => $players
            );
            $players = get_posts( $args );
        }
        ?>

        <div id="wpcm-player-roster-stats">
            <table>
                <?php
                if( $players != null ) { ?>
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php _e( 'Name', 'wp-club-manager' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                <?php
                } ?>
                <tbody>

                <?php
                foreach ( $players as $player ) { ?>

                    <tr data-club="<?php echo $player->ID; ?>">

                        <td>
                            <input type="checkbox" name="record">
                        </td>
                        <td class="club">
                            <input type="hidden" name="wpcm_roster_players[]" value="<?php echo $player->ID; ?>" />
                            <?php echo $player->post_title; ?>
                        </td>
                        <td class="roster-actions">
                            <a class="" href="<?php echo get_edit_post_link( $player->ID ); ?>"><?php _e( 'Edit', 'wp-club-manager' ); ?></a>
                        </td>

                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>

        <div class="wpcm-table-stats-footer clearfix">

            <div class="add-club">
                <?php
                wpcm_dropdown_posts(array(
                    'class'             => 'player-id',
                    'name' 				=> 'roster_players',
                    'post_type' 		=> 'wpcm_player',
                    'limit' 			=> -1,
                    'show_option_none'	=> __( 'Choose a player', 'wp-club-manager' )
                ));
                ?>

                <input type="button" class="button-secondary wpcm-player-roster-add-row" value="<?php _e( 'Add player', 'wp-club-manager' ); ?>">
            </div>

            <a class="wpcm-player-roster-delete-row <?php echo ( $players != null ? '' : 'hidden-button' ); ?>"><?php _e( 'Remove selected', 'wp-club-manager' ); ?></a>

        </div>

    <?php
    }

    /**
     * Save meta box data
     */
    public static function save( $post_id, $post ) {

        if( isset( $_POST['wpcm_roster_players'] ) ){
            $players = $_POST['wpcm_roster_players'];
        } else {
            $players = array();
        }

        if( is_array( $players ) ) {
            $teams = wp_get_post_terms( $post_id, 'wpcm_team' );
            $team = $teams[0]->term_id;
            foreach( $players as $player ) {
                wp_set_post_terms( $player, $team, 'wpcm_team', true );
            }
            $seasons = wp_get_post_terms( $post_id, 'wpcm_season' );
            $season = $seasons[0]->term_id;
            foreach( $players as $player ) {
                wp_set_post_terms( $player, $season, 'wpcm_season', true );
            }
        }

        update_post_meta( $post_id, '_wpcm_roster_players', serialize( $players ) );

        do_action( 'delete_plugin_transients' );
    }
}