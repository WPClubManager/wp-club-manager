<?php
/**
 * Update WPClubManager to 1.1.0
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Updates
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

delete_option('wpcm_player_profile_show_appearances');
delete_option('wpcm_player_profile_show_goals');
delete_option('wpcm_player_profile_show_assists');
delete_option('wpcm_player_profile_show_yellowcards');
delete_option('wpcm_player_profile_show_redcards');
delete_option('wpcm_player_profile_show_ratings');
delete_option('wpcm_player_profile_show_mvp');
delete_option('wpcm_player_goals_label');
delete_option('wpcm_player_assists_label');
delete_option('wpcm_player_yellowcards_label');
delete_option('wpcm_player_redcards_label');
delete_option('wpcm_player_rating_label');
delete_option('wpcm_player_ratings_label');
delete_option('wpcm_player_mvp_label');