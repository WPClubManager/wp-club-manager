<?php
/**
 * Preset Functions
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     2.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Sports presets
 *
 * Get an array of sport options and settings.
 *
 * @return array
 */
function wpcm_get_sport_presets() {
	return apply_filters( 'wpcm_sports', array(
		'baseball'     => array(
			'name'              => __( 'Baseball', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => '',
						'slug' => '',
					),
				),
			),
			'stats_labels'      => array(
				'ab'     => array(
					'name'  => __( 'At Bats', 'wp-club-manager' ),
					'label' => _x( 'AB', 'At Bats', 'wp-club-manager' ),
				),
				'h'      => array(
					'name'  => __( 'Hits', 'wp-club-manager' ),
					'label' => _x( 'H', 'Hits', 'wp-club-manager' ),
				),
				'r'      => array(
					'name'  => __( 'Runs', 'wp-club-manager' ),
					'label' => _x( 'R', 'Runs', 'wp-club-manager' ),
				),
				'er'     => array(
					'name'  => __( 'Earned Runs', 'wp-club-manager' ),
					'label' => _x( 'ER', 'Earned Runs', 'wp-club-manager' ),
				),
				'hr'     => array(
					'name'  => __( 'Home Runs', 'wp-club-manager' ),
					'label' => _x( 'HR', 'Home Runs', 'wp-club-manager' ),
				),
				'2b'     => array(
					'name'  => __( 'Doubles', 'wp-club-manager' ),
					'label' => _x( '2B', 'Doubles', 'wp-club-manager' ),
				),
				'3b'     => array(
					'name'  => __( 'Triples', 'wp-club-manager' ),
					'label' => _x( '3B', 'Triples', 'wp-club-manager' ),
				),
				'rbi'    => array(
					'name'  => __( 'Runs Batted In', 'wp-club-manager' ),
					'label' => _x( 'RBI', 'Runs Batted In', 'wp-club-manager' ),
				),
				'bb'     => array(
					'name'  => __( 'Bases on Bulk', 'wp-club-manager' ),
					'label' => _x( 'BB', 'Bases on Bulk', 'wp-club-manager' ),
				),
				'so'     => array(
					'name'  => __( 'Strike Outs', 'wp-club-manager' ),
					'label' => _x( 'SO', 'Strike Outs', 'wp-club-manager' ),
				),
				'sb'     => array(
					'name'  => __( 'Stolen Bases', 'wp-club-manager' ),
					'label' => _x( 'SB', 'Stolen Bases', 'wp-club-manager' ),
				),
				'cs'     => array(
					'name'  => __( 'Caught Stealing', 'wp-club-manager' ),
					'label' => _x( 'CS', 'Caught Stealing', 'wp-club-manager' ),
				),
				'tc'     => array(
					'name'  => __( 'Total Chances', 'wp-club-manager' ),
					'label' => _x( 'TC', 'Total Chances', 'wp-club-manager' ),
				),
				'po'     => array(
					'name'  => __( 'Putouts', 'wp-club-manager' ),
					'label' => _x( 'PO', 'Putouts', 'wp-club-manager' ),
				),
				'a'      => array(
					'name'  => __( 'Assists', 'wp-club-manager' ),
					'label' => _x( 'A', 'Assists', 'wp-club-manager' ),
				),
				'e'      => array(
					'name'  => __( 'Errors', 'wp-club-manager' ),
					'label' => _x( 'E', 'Errors', 'wp-club-manager' ),
				),
				'dp'     => array(
					'name'  => __( 'Double Plays', 'wp-club-manager' ),
					'label' => _x( 'DP', 'Double Plays', 'wp-club-manager' ),
				),
				'rating' => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'    => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'pct' => array(
					'name'  => __( 'Win Percentage', 'wp-club-manager' ),
					'label' => _x( 'PCT', 'Win Percentage', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Runs For', 'wp-club-manager' ),
					'label' => _x( 'RF', 'Runs For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Runs Against', 'wp-club-manager' ),
					'label' => _x( 'RA', 'Runs Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Run Difference', 'wp-club-manager' ),
					'label' => _x( 'RD', 'Run Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'basketball'   => array(
			'name'              => __( 'Basketball', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Point Guard',
						'slug' => 'pointguard',
					),
					array(
						'name' => 'Shooting Guard',
						'slug' => 'shootingguard',
					),
					array(
						'name' => 'Small Forward',
						'slug' => 'smallforward',
					),
					array(
						'name' => 'Power Forward',
						'slug' => 'powerforward',
					),
					array(
						'name' => 'Center',
						'slug' => 'center',
					),
				),
			),
			'stats_labels'      => array(
				'min'    => array(
					'name'  => __( 'Minutes', 'wp-club-manager' ),
					'label' => _x( 'MIN', 'Minutes', 'wp-club-manager' ),
				),
				'fgm'    => array(
					'name'  => __( 'Field Goals Made', 'wp-club-manager' ),
					'label' => _x( 'FGM', 'Field Goals Made', 'wp-club-manager' ),
				),
				'fga'    => array(
					'name'  => __( 'Field Goals Attempted', 'wp-club-manager' ),
					'label' => _x( 'FGA', 'Field Goals Attempted', 'wp-club-manager' ),
				),
				'3pm'    => array(
					'name'  => __( '3 Points Made', 'wp-club-manager' ),
					'label' => _x( '3PM', '3 Points Made', 'wp-club-manager' ),
				),
				'3pa'    => array(
					'name'  => __( '3 Points Attempted', 'wp-club-manager' ),
					'label' => _x( '3PA', '3 Points Attempted', 'wp-club-manager' ),
				),
				'ftm'    => array(
					'name'  => __( 'Free Throws Made', 'wp-club-manager' ),
					'label' => _x( 'FTM', 'Free Throws Made', 'wp-club-manager' ),
				),
				'fta'    => array(
					'name'  => __( 'Free Throws Attempted', 'wp-club-manager' ),
					'label' => _x( 'FTA', 'Free Throws Attempted', 'wp-club-manager' ),
				),
				'or'     => array(
					'name'  => __( 'Offensive Rebounds', 'wp-club-manager' ),
					'label' => _x( 'OR', 'Offensive Rebounds', 'wp-club-manager' ),
				),
				'dr'     => array(
					'name'  => __( 'Defensive Rebounds', 'wp-club-manager' ),
					'label' => _x( 'DR', 'Defensive Rebounds', 'wp-club-manager' ),
				),
				'reb'    => array(
					'name'  => __( 'Rebounds', 'wp-club-manager' ),
					'label' => _x( 'REB', 'Rebounds', 'wp-club-manager' ),
				),
				'ast'    => array(
					'name'  => __( 'Assists', 'wp-club-manager' ),
					'label' => _x( 'AST', 'Assists', 'wp-club-manager' ),
				),
				'blk'    => array(
					'name'  => __( 'Blocks', 'wp-club-manager' ),
					'label' => _x( 'BLK', 'Blocks', 'wp-club-manager' ),
				),
				'stl'    => array(
					'name'  => __( 'Steals', 'wp-club-manager' ),
					'label' => _x( 'STL', 'Steals', 'wp-club-manager' ),
				),
				'pf'     => array(
					'name'  => __( 'Personal Fouls', 'wp-club-manager' ),
					'label' => _x( 'PF', 'Personal Fouls', 'wp-club-manager' ),
				),
				'to'     => array(
					'name'  => __( 'Turnovers', 'wp-club-manager' ),
					'label' => _x( 'TO', 'Turnovers', 'wp-club-manager' ),
				),
				'pts'    => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'PTS', 'Points', 'wp-club-manager' ),
				),
				'rating' => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'    => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'otw' => array(
					'name'  => __( 'Overtime Wins', 'wp-club-manager' ),
					'label' => _x( 'OTW', 'Overtime Wins', 'wp-club-manager' ),
				),
				'otl' => array(
					'name'  => __( 'Overtime Losses', 'wp-club-manager' ),
					'label' => _x( 'OTL', 'Overtime Losses', 'wp-club-manager' ),
				),
				'pct' => array(
					'name'  => __( 'Win Percentage', 'wp-club-manager' ),
					'label' => _x( 'PCT', 'Win Percentage', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Points For', 'wp-club-manager' ),
					'label' => _x( 'PF', 'Points For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Points Against', 'wp-club-manager' ),
					'label' => _x( 'PA', 'Points Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Points Difference', 'wp-club-manager' ),
					'label' => _x( 'PD', 'Points Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'cricket'      => array(
			'name'              => __( 'Cricket', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Batsman',
						'slug' => 'batsman',
					),
					array(
						'name' => 'Bowler',
						'slug' => 'bowler',
					),
					array(
						'name' => 'Wicket-keeper',
						'slug' => 'wicket-keeper',
					),
					array(
						'name' => 'Fielder',
						'slug' => 'fielder',
					),
					array(
						'name' => 'All-rounder',
						'slug' => 'all-rounder',
					),

				),
			),
			'stat_sections'     => array(
				'batting' => __( 'Batting &amp; Fielding', 'wp-club-manager' ),
				'bowling' => __( 'Bowling', 'wp-club-manager' ),
			),
			'stats_labels'      => array(
				'innings'      => array(
					'name'    => __( 'Innings', 'wp-club-manager' ),
					'label'   => _x( 'I', 'Innings', 'wp-club-manager' ),
					'section' => 'batting',
				),
				'runs'         => array(
					'name'    => __( 'Runs', 'wp-club-manager' ),
					'label'   => _x( 'R', 'Runs', 'wp-club-manager' ),
					'section' => 'batting',
				),
				'balls'        => array(
					'name'    => __( 'Balls', 'wp-club-manager' ),
					'label'   => _x( 'B', 'Balls', 'wp-club-manager' ),
					'section' => 'batting',
				),
				'fours'        => array(
					'name'    => __( 'Fours', 'wp-club-manager' ),
					'label'   => _x( '4s', 'Fours', 'wp-club-manager' ),
					'section' => 'batting',
				),
				'sixes'        => array(
					'name'    => __( 'Sixes', 'wp-club-manager' ),
					'label'   => _x( '6s', 'Sixes', 'wp-club-manager' ),
					'section' => 'batting',
				),
				'catches'      => array(
					'name'    => __( 'Catches', 'wp-club-manager' ),
					'label'   => _x( 'CT', 'Catches', 'wp-club-manager' ),
					'section' => 'batting',
				),
				'stumpings'    => array(
					'name'    => __( 'Stumpings', 'wp-club-manager' ),
					'label'   => _x( 'ST', 'Stumpings', 'wp-club-manager' ),
					'section' => 'batting',
				),
				'overs'        => array(
					'name'    => __( 'Overs', 'wp-club-manager' ),
					'label'   => _x( 'O', 'Overs', 'wp-club-manager' ),
					'section' => 'bowling',
				),
				'maidens'      => array(
					'name'    => __( 'Maidens', 'wp-club-manager' ),
					'label'   => _x( 'M', 'Maidens', 'wp-club-manager' ),
					'section' => 'bowling',
				),
				'runs_against' => array(
					'name'    => __( 'Runs', 'wp-club-manager' ),
					'label'   => _x( 'R', 'Runs Against', 'wp-club-manager' ),
					'section' => 'bowling',
				),
				'wickets'      => array(
					'name'    => __( 'Wickets', 'wp-club-manager' ),
					'label'   => _x( 'W', 'Wickets', 'wp-club-manager' ),
					'section' => 'bowling',
				),
				'mvp'          => array(
					'name'    => __( 'Player of Match', 'wp-club-manager' ),
					'label'   => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
					'section' => 'none',
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Runs For', 'wp-club-manager' ),
					'label' => _x( 'RF', 'Runs For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Runs Against', 'wp-club-manager' ),
					'label' => _x( 'RA', 'Runs Against', 'wp-club-manager' ),
				),
			),
		),
		'floorball'    => array(
			'name'              => __( 'Floorball', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Defender',
						'slug' => 'defender',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels'      => array(
				'g'         => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'G', 'Goals', 'wp-club-manager' ),
				),
				'a'         => array(
					'name'  => __( 'Assists', 'wp-club-manager' ),
					'label' => _x( 'A', 'Assists', 'wp-club-manager' ),
				),
				'plusminus' => array(
					'name'  => __( 'Plus/Minus Rating', 'wp-club-manager' ),
					'label' => _x( '+/-', 'Plus/Minus Rating', 'wp-club-manager' ),
				),
				'sog'       => array(
					'name'  => __( 'Shots on Goal', 'wp-club-manager' ),
					'label' => _x( 'SOG', 'Shots on Goal', 'wp-club-manager' ),
				),
				'pim'       => array(
					'name'  => __( 'Penalty Minutes', 'wp-club-manager' ),
					'label' => _x( 'PM', 'Penalty Minutes', 'wp-club-manager' ),
				),
				'redcards'  => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'sav'       => array(
					'name'  => __( 'Saves', 'wp-club-manager' ),
					'label' => _x( 'SAV', 'Saves', 'wp-club-manager' ),
				),
				'ga'        => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'GA', 'Goals Against', 'wp-club-manager' ),
				),
				'rating'    => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'       => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Goals Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goal Difference', 'wp-club-manager' ),
					'label' => _x( 'GD', 'Goal Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'football'     => array(
			'name'              => __( 'American Football', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Quarterback',
						'slug' => 'quarterback',
					),
					array(
						'name' => 'Running Back',
						'slug' => 'runningback',
					),
					array(
						'name' => 'Wide Receiver',
						'slug' => 'widereceiver',
					),
					array(
						'name' => 'Tight End',
						'slug' => 'tightend',
					),
					array(
						'name' => 'Defensive Lineman',
						'slug' => 'defensivelineman',
					),
					array(
						'name' => 'Linebacker',
						'slug' => 'linebacker',
					),
					array(
						'name' => 'Defensive Back',
						'slug' => 'defensiveback',
					),
					array(
						'name' => 'Kickoff Kicker',
						'slug' => 'kickoffkicker',
					),
					array(
						'name' => 'Kick Returner',
						'slug' => 'kickreturner',
					),
					array(
						'name' => 'Punter',
						'slug' => 'punter',
					),
					array(
						'name' => 'Punt Returner',
						'slug' => 'puntreturner',
					),
					array(
						'name' => 'Field Goal Kicker',
						'slug' => 'fieldgoalkicker',
					),
				),
			),
			'stats_labels'      => array(
				'pa_cmp'   => array(
					'name'  => __( 'Pass Completions', 'wp-club-manager' ),
					'label' => _x( 'CMP', 'Pass Completions', 'wp-club-manager' ),
				),
				'pa_yds'   => array(
					'name'  => __( 'Passing Yards', 'wp-club-manager' ),
					'label' => _x( 'YDS', 'Passing Yards', 'wp-club-manager' ),
				),
				'sc_pass'  => array(
					'name'  => __( 'Passing Touchdowns', 'wp-club-manager' ),
					'label' => _x( 'PASS', 'Passing Touchdowns', 'wp-club-manager' ),
				),
				'pa_int'   => array(
					'name'  => __( 'Passing Interceptions', 'wp-club-manager' ),
					'label' => _x( 'INT', 'Passing Interceptions', 'wp-club-manager' ),
				),
				'ru_yds'   => array(
					'name'  => __( 'Rushing Yards', 'wp-club-manager' ),
					'label' => _x( 'YDS', 'Rushing Yards', 'wp-club-manager' ),
				),
				'sc_rush'  => array(
					'name'  => __( 'Rushing Touchdowns', 'wp-club-manager' ),
					'label' => _x( 'RUSH', 'Rushing Touchdowns', 'wp-club-manager' ),
				),
				're_rec'   => array(
					'name'  => __( 'Receptions', 'wp-club-manager' ),
					'label' => _x( 'REC', 'Receptions', 'wp-club-manager' ),
				),
				're_yds'   => array(
					'name'  => __( 'Receiving Yards', 'wp-club-manager' ),
					'label' => _x( 'YDS', 'Receiving Yards', 'wp-club-manager' ),
				),
				'sc_rec'   => array(
					'name'  => __( 'Receiving Touchdowns', 'wp-club-manager' ),
					'label' => _x( 'REC', 'Receiving Touchdowns', 'wp-club-manager' ),
				),
				'de_total' => array(
					'name'  => __( 'Total Tackles', 'wp-club-manager' ),
					'label' => _x( 'TOTAL', 'Total Tackles', 'wp-club-manager' ),
				),
				'de_sack'  => array(
					'name'  => __( 'Sacks', 'wp-club-manager' ),
					'label' => _x( 'SACK', 'Sacks', 'wp-club-manager' ),
				),
				'de_ff'    => array(
					'name'  => __( 'Fumbles', 'wp-club-manager' ),
					'label' => _x( 'FF', 'Fumbles', 'wp-club-manager' ),
				),
				'de_int'   => array(
					'name'  => __( 'Interceptions', 'wp-club-manager' ),
					'label' => _x( 'INT', 'Interceptions', 'wp-club-manager' ),
				),
				'de_kb'    => array(
					'name'  => __( 'Blocked Kicks', 'wp-club-manager' ),
					'label' => _x( 'KB', 'Blocked Kicks', 'wp-club-manager' ),
				),
				'sc_td'    => array(
					'name'  => __( 'Touchdowns', 'wp-club-manager' ),
					'label' => _x( 'T', 'Touchdowns', 'wp-club-manager' ),
				),
				'sc_2pt'   => array(
					'name'  => __( '2 Point Conversions', 'wp-club-manager' ),
					'label' => _x( '2PT', '2 Point Conversions', 'wp-club-manager' ),
				),
				'sc_fg'    => array(
					'name'  => __( 'Field Goals', 'wp-club-manager' ),
					'label' => _x( 'FG', 'Field Goals', 'wp-club-manager' ),
				),
				'sc_pat'   => array(
					'name'  => __( 'Extra Points', 'wp-club-manager' ),
					'label' => _x( 'PAT', 'Extra Points', 'wp-club-manager' ),
				),
				'sc_pts'   => array(
					'name'  => __( 'Total Points', 'wp-club-manager' ),
					'label' => _x( 'PTS', 'Total Points', 'wp-club-manager' ),
				),
				'rating'   => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'      => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Tie', 'wp-club-manager' ),
					'label' => _x( 'T', 'Tie', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Points For', 'wp-club-manager' ),
					'label' => _x( 'PF', 'Points For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Points Against', 'wp-club-manager' ),
					'label' => _x( 'PA', 'Points Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Points Difference', 'wp-club-manager' ),
					'label' => _x( 'PD', 'Points Difference', 'wp-club-manager' ),
				),
				'pct' => array(
					'name'  => __( 'Win Percentage', 'wp-club-manager' ),
					'label' => _x( 'PCT', 'Win Percentage', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'footy'        => array(
			'name'              => __( 'Australian Rules Football', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Full Back',
						'slug' => 'full-back',
					),
					array(
						'name' => 'Back Pocket',
						'slug' => 'back-pocket',
					),
					array(
						'name' => 'Centre Half-Back',
						'slug' => 'centre-half-back',
					),
					array(
						'name' => 'Half-Back Flank',
						'slug' => 'half-back-flank',
					),
					array(
						'name' => 'Centre Half-Forward',
						'slug' => 'centre-half-forward',
					),
					array(
						'name' => 'Half-Forward Flank',
						'slug' => 'half-forward-flank',
					),
					array(
						'name' => 'Full Forward',
						'slug' => 'full-forward',
					),
					array(
						'name' => 'Forward Pocket',
						'slug' => 'forward-pocket',
					),
					array(
						'name' => 'Follower',
						'slug' => 'follower',
					),
					array(
						'name' => 'Inside Midfield',
						'slug' => 'inside-midfield',
					),
					array(
						'name' => 'Outside Midfield',
						'slug' => 'outside-midfield',
					),
				),
			),
			'stats_labels'      => array(
				'k'           => array(
					'name'  => __( 'Kicks', 'wp-club-manager' ),
					'label' => _x( 'K', 'Kicks', 'wp-club-manager' ),
				),
				'hb'          => array(
					'name'  => __( 'Handballs', 'wp-club-manager' ),
					'label' => _x( 'HB', 'Handballs', 'wp-club-manager' ),
				),
				'd'           => array(
					'name'  => __( 'Disposals', 'wp-club-manager' ),
					'label' => _x( 'D', 'Disposals', 'wp-club-manager' ),
				),
				'cp'          => array(
					'name'  => __( 'Contested Possesion', 'wp-club-manager' ),
					'label' => _x( 'CP', 'Contested Possesion', 'wp-club-manager' ),
				),
				'm'           => array(
					'name'  => __( 'Marks', 'wp-club-manager' ),
					'label' => _x( 'M', 'Marks', 'wp-club-manager' ),
				),
				'cm'          => array(
					'name'  => __( 'Contested Marks', 'wp-club-manager' ),
					'label' => _x( 'CM', 'Contested Marks', 'wp-club-manager' ),
				),
				'ff'          => array(
					'name'  => __( 'Frees For', 'wp-club-manager' ),
					'label' => _x( 'FF', 'Frees For', 'wp-club-manager' ),
				),
				'fa'          => array(
					'name'  => __( 'Frees Against', 'wp-club-manager' ),
					'label' => _x( 'FA', 'Frees Against', 'wp-club-manager' ),
				),
				'clg'         => array(
					'name'  => __( 'Clangers', 'wp-club-manager' ),
					'label' => _x( 'C', 'Clangers', 'wp-club-manager' ),
				),
				'tkl'         => array(
					'name'  => __( 'Tackles', 'wp-club-manager' ),
					'label' => _x( 'T', 'Tackles', 'wp-club-manager' ),
				),
				'i50'         => array(
					'name'  => __( 'Inside 50s', 'wp-club-manager' ),
					'label' => _x( 'I50', 'Inside 50s', 'wp-club-manager' ),
				),
				'r50'         => array(
					'name'  => __( 'Rebound 50s', 'wp-club-manager' ),
					'label' => _x( 'R50', 'Rebound 50s', 'wp-club-manager' ),
				),
				'1pct'        => array(
					'name'  => __( 'One-Percenters', 'wp-club-manager' ),
					'label' => _x( '1PCT', 'One-Percenters', 'wp-club-manager' ),
				),
				'ho'          => array(
					'name'  => __( 'Hit Outs', 'wp-club-manager' ),
					'label' => _x( 'HO', 'Hit Outs', 'wp-club-manager' ),
				),
				'g'           => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'G', 'Goals', 'wp-club-manager' ),
				),
				'b'           => array(
					'name'  => __( 'Behinds', 'wp-club-manager' ),
					'label' => _x( 'B', 'Behinds', 'wp-club-manager' ),
				),
				'yellowcards' => array(
					'name'  => __( 'Yellow Cards', 'wp-club-manager' ),
					'label' => _x( 'YC', 'Yellow Cards', 'wp-club-manager' ),
				),
				'redcards'    => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Points For', 'wp-club-manager' ),
					'label' => _x( 'PF', 'Points For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Points Against', 'wp-club-manager' ),
					'label' => _x( 'PA', 'Points Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Points Difference', 'wp-club-manager' ),
					'label' => _x( 'PD%', 'Points Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'gaelic'       => array(
			'name'              => __( 'Gaelic Football / Hurling', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Defender',
						'slug' => 'defender',
					),
					array(
						'name' => 'Midfielder',
						'slug' => 'midfielder',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels'      => array(
				'g'           => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'G', 'Goals', 'wp-club-manager' ),
				),
				'pts'         => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'P', 'Points', 'wp-club-manager' ),
				),
				'gff'         => array(
					'name'  => __( 'Goals From Frees', 'wp-club-manager' ),
					'label' => _x( 'GFF', 'Goals From Frees', 'wp-club-manager' ),
				),
				'sog'         => array(
					'name'  => __( 'Points From Frees', 'wp-club-manager' ),
					'label' => _x( 'PFF', 'Points From Frees', 'wp-club-manager' ),
				),
				'yellowcards' => array(
					'name'  => __( 'Yellow Cards', 'wp-club-manager' ),
					'label' => _x( 'YC', 'Yellow Cards', 'wp-club-manager' ),
				),
				'blackcards'  => array(
					'name'  => __( 'Black Cards', 'wp-club-manager' ),
					'label' => _x( 'BC', 'Black Cards', 'wp-club-manager' ),
				),
				'redcards'    => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Goals Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goal Difference', 'wp-club-manager' ),
					'label' => _x( 'GD', 'Goal Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'handball'     => array(
			'name'              => __( 'Handball', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Left Wing',
						'slug' => 'left-wing',
					),
					array(
						'name' => 'Left Back',
						'slug' => 'left-back',
					),
					array(
						'name' => 'Center',
						'slug' => 'center',
					),
					array(
						'name' => 'Right Wing',
						'slug' => 'right-wing',
					),
					array(
						'name' => 'Right Back',
						'slug' => 'right-back',
					),
					array(
						'name' => 'Pivot',
						'slug' => 'pivot',
					),
				),
			),
			'stats_labels'      => array(
				'goals'       => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'GLS', 'Goals', 'wp-club-manager' ),
				),
				'2min'        => array(
					'name'  => __( '2 Minute Suspension', 'wp-club-manager' ),
					'label' => _x( '2MIN', '2 Minute Suspension', 'wp-club-manager' ),
				),
				'yellowcards' => array(
					'name'  => __( 'Yellow Cards', 'wp-club-manager' ),
					'label' => _x( 'YC', 'Yellow Cards', 'wp-club-manager' ),
				),
				'redcards'    => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Goals Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goal Difference', 'wp-club-manager' ),
					'label' => _x( 'GD', 'Goal Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'hockey_field' => array(
			'name'              => __( 'Field Hockey', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goalie',
						'slug' => 'goalie',
					),
					array(
						'name' => 'Defence',
						'slug' => 'defence',
					),
					array(
						'name' => 'Midfield',
						'slug' => 'midfield',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels'      => array(
				'gls'         => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'G', 'Goals', 'wp-club-manager' ),
				),
				'ass'         => array(
					'name'  => __( 'Assists', 'wp-club-manager' ),
					'label' => _x( 'A', 'Assists', 'wp-club-manager' ),
				),
				'sho'         => array(
					'name'  => __( 'Shots', 'wp-club-manager' ),
					'label' => _x( 'SH', 'Shots', 'wp-club-manager' ),
				),
				'sog'         => array(
					'name'  => __( 'Shots on Goal', 'wp-club-manager' ),
					'label' => _x( 'SOG', 'Shots on Goal', 'wp-club-manager' ),
				),
				'sav'         => array(
					'name'  => __( 'Saves', 'wp-club-manager' ),
					'label' => _x( 'SAV', 'Saves', 'wp-club-manager' ),
				),
				'greencards'  => array(
					'name'  => __( 'Green Cards', 'wp-club-manager' ),
					'label' => _x( 'GC', 'Green Cards', 'wp-club-manager' ),
				),
				'yellowcards' => array(
					'name'  => __( 'Yellow Cards', 'wp-club-manager' ),
					'label' => _x( 'YC', 'Yellow Cards', 'wp-club-manager' ),
				),
				'redcards'    => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Goals Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goal Difference', 'wp-club-manager' ),
					'label' => _x( 'GD', 'Goal Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'hockey'       => array(
			'name'              => __( 'Ice Hockey', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goalie',
						'slug' => 'goalie',
					),
					array(
						'name' => 'Defense',
						'slug' => 'defense',
					),
					array(
						'name' => 'Center',
						'slug' => 'center',
					),
					array(
						'name' => 'Right Wing',
						'slug' => 'right-wing',
					),
					array(
						'name' => 'Left Wing',
						'slug' => 'left-wing',
					),
				),
			),
			'stats_labels'      => array(
				'g'         => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'G', 'Goals', 'wp-club-manager' ),
				),
				'a'         => array(
					'name'  => __( 'Assists', 'wp-club-manager' ),
					'label' => _x( 'A', 'Assists', 'wp-club-manager' ),
				),
				'pts'       => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'P', 'Points', 'wp-club-manager' ),
				),
				'plusminus' => array(
					'name'  => __( 'Plus/Minus Rating', 'wp-club-manager' ),
					'label' => _x( '+/-', 'Plus/Minus Rating', 'wp-club-manager' ),
				),
				'sog'       => array(
					'name'  => __( 'Shots On Goal', 'wp-club-manager' ),
					'label' => _x( 'SOG', 'Shots On Goal', 'wp-club-manager' ),
				),
				'ms'        => array(
					'name'  => __( 'Missed Shots', 'wp-club-manager' ),
					'label' => _x( 'MS', 'Missed Shots', 'wp-club-manager' ),
				),
				'bs'        => array(
					'name'  => __( 'Blocked Shots', 'wp-club-manager' ),
					'label' => _x( 'BS', 'Blocked Shots', 'wp-club-manager' ),
				),
				'pim'       => array(
					'name'  => __( 'Penalty Minutes', 'wp-club-manager' ),
					'label' => _x( 'PIM', 'Penalty Minutes', 'wp-club-manager' ),
				),
				'ht'        => array(
					'name'  => __( 'Hits', 'wp-club-manager' ),
					'label' => _x( 'HT', 'Hits', 'wp-club-manager' ),
				),
				'fw'        => array(
					'name'  => __( 'Faceoffs Won', 'wp-club-manager' ),
					'label' => _x( 'FW', 'Faceoffs Won', 'wp-club-manager' ),
				),
				'fl'        => array(
					'name'  => __( 'Faceoffs Lost', 'wp-club-manager' ),
					'label' => _x( 'FL', 'Faceoffs Lost', 'wp-club-manager' ),
				),
				'sav'       => array(
					'name'  => __( 'Saves', 'wp-club-manager' ),
					'label' => _x( 'SAV', 'Saves', 'wp-club-manager' ),
				),
				'rating'    => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'       => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'otw' => array(
					'name'  => __( 'Overtime Wins', 'wp-club-manager' ),
					'label' => _x( 'OTW', 'Overtime Wins', 'wp-club-manager' ),
				),
				'otl' => array(
					'name'  => __( 'Overtime Losses', 'wp-club-manager' ),
					'label' => _x( 'OTL', 'Overtime Losses', 'wp-club-manager' ),
				),
				'pct' => array(
					'name'  => __( 'Win Percentage', 'wp-club-manager' ),
					'label' => _x( 'PCT', 'Win Percentage', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Goals Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goal Difference', 'wp-club-manager' ),
					'label' => _x( 'GD', 'Goal Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'lacrosse'     => array(
			'name'              => __( 'Lacrosse', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goalie',
						'slug' => 'goalie',
					),
					array(
						'name' => 'Defender',
						'slug' => 'defender',
					),
					array(
						'name' => 'Midfielder',
						'slug' => 'midfielder',
					),
					array(
						'name' => 'Attack',
						'slug' => 'attack',
					),
				),
			),
			'stats_labels'      => array(
				'goals'       => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'GLS', 'Goals', 'wp-club-manager' ),
				),
				'assists'     => array(
					'name'  => __( 'Assists', 'wp-club-manager' ),
					'label' => _x( 'AST', 'Assists', 'wp-club-manager' ),
				),
				'groundballs' => array(
					'name'  => __( 'Ground Balls', 'wp-club-manager' ),
					'label' => _x( 'GRB', 'Ground Balls', 'wp-club-manager' ),
				),
				'saves'       => array(
					'name'  => __( 'Saves', 'wp-club-manager' ),
					'label' => _x( 'SAV', 'Saves', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Played', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goal Difference', 'wp-club-manager' ),
					'label' => _x( 'GD', 'Goal Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'netball'      => array(
			'name'              => __( 'Netball', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goal Shooter',
						'slug' => 'goal-shooter',
					),
					array(
						'name' => 'Goal Attack',
						'slug' => 'goal-attack',
					),
					array(
						'name' => 'Wing Attack',
						'slug' => 'wing-attack',
					),
					array(
						'name' => 'Centre',
						'slug' => 'centre',
					),
					array(
						'name' => 'Wing Defence',
						'slug' => 'wing-defence',
					),
					array(
						'name' => 'Goal Defence',
						'slug' => 'goal-defence',
					),
					array(
						'name' => 'Goal Keeper',
						'slug' => 'goal-keeper',
					),
				),
			),
			'stats_labels'      => array(
				'g'      => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'GLS', 'Goals', 'wp-club-manager' ),
				),
				'gatt'   => array(
					'name'  => __( 'Goal Attempts', 'wp-club-manager' ),
					'label' => _x( 'ATT', 'Goal Attempts', 'wp-club-manager' ),
				),
				'gass'   => array(
					'name'  => __( 'Goal Assists', 'wp-club-manager' ),
					'label' => _x( 'AST', 'Goal Assists', 'wp-club-manager' ),
				),
				'rbs'    => array(
					'name'  => __( 'Rebounds', 'wp-club-manager' ),
					'label' => _x( 'REB', 'Rebounds', 'wp-club-manager' ),
				),
				'cpr'    => array(
					'name'  => __( 'Center Pass Receives', 'wp-club-manager' ),
					'label' => _x( 'CPR', 'Center Pass Receives', 'wp-club-manager' ),
				),
				'int'    => array(
					'name'  => __( 'Interceptions', 'wp-club-manager' ),
					'label' => _x( 'INT', 'Interceptions', 'wp-club-manager' ),
				),
				'def'    => array(
					'name'  => __( 'Deflections', 'wp-club-manager' ),
					'label' => _x( 'DEF', 'Deflections', 'wp-club-manager' ),
				),
				'pen'    => array(
					'name'  => __( 'Penalties', 'wp-club-manager' ),
					'label' => _x( 'PEN', 'Penalties', 'wp-club-manager' ),
				),
				'to'     => array(
					'name'  => __( 'Turnovers', 'wp-club-manager' ),
					'label' => _x( 'TO', 'Turnovers', 'wp-club-manager' ),
				),
				'rating' => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'    => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Goals Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goals Difference', 'wp-club-manager' ),
					'label' => _x( 'PD', 'Goals Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'rugby_league' => array(
			'name'              => __( 'Rugby League', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Scrum Half',
						'slug' => 'scrum-half',
					),
					array(
						'name' => 'Stand Off',
						'slug' => 'stand-off',
					),
					array(
						'name' => 'Centre',
						'slug' => 'centre',
					),
					array(
						'name' => 'Winger',
						'slug' => 'winger',
					),
					array(
						'name' => 'Full Back',
						'slug' => 'full-back',
					),
					array(
						'name' => 'Prop',
						'slug' => 'prop',
					),
					array(
						'name' => 'Hooker',
						'slug' => 'hooker',
					),
					array(
						'name' => '2nd Row',
						'slug' => 'second-row',
					),
					array(
						'name' => 'Lock',
						'slug' => 'lock',
					),
				),
			),
			'stats_labels'      => array(
				't'           => array(
					'name'  => __( 'Tries', 'wp-club-manager' ),
					'label' => _x( 'TR', 'Tries', 'wp-club-manager' ),
				),
				'c'           => array(
					'name'  => __( 'Conversions', 'wp-club-manager' ),
					'label' => _x( 'CON', 'Conversions', 'wp-club-manager' ),
				),
				'p'           => array(
					'name'  => __( 'Penalties', 'wp-club-manager' ),
					'label' => _x( 'PEN', 'Penalties', 'wp-club-manager' ),
				),
				'dg'          => array(
					'name'  => __( 'Drop Goals', 'wp-club-manager' ),
					'label' => _x( 'DG', 'Drop Goals', 'wp-club-manager' ),
				),
				'yellowcards' => array(
					'name'  => __( 'Yellow Cards', 'wp-club-manager' ),
					'label' => _x( 'YC', 'Yellow Cards', 'wp-club-manager' ),
				),
				'redcards'    => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Points For', 'wp-club-manager' ),
					'label' => _x( 'PF', 'Points For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Points Against', 'wp-club-manager' ),
					'label' => _x( 'PA', 'Points Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Points Difference', 'wp-club-manager' ),
					'label' => _x( 'PD', 'Points Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'rugby'        => array(
			'name'              => __( 'Rugby Union', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Scrum Half',
						'slug' => 'scrum-half',
					),
					array(
						'name' => 'Fly Half',
						'slug' => 'fly-half',
					),
					array(
						'name' => 'Centre',
						'slug' => 'centre',
					),
					array(
						'name' => 'Winger',
						'slug' => 'winger',
					),
					array(
						'name' => 'Full Back',
						'slug' => 'full-back',
					),
					array(
						'name' => 'Prop',
						'slug' => 'prop',
					),
					array(
						'name' => 'Hooker',
						'slug' => 'hooker',
					),
					array(
						'name' => 'Lock',
						'slug' => 'lock',
					),
					array(
						'name' => 'Flanker',
						'slug' => 'flanker',
					),
					array(
						'name' => 'No. 8',
						'slug' => 'no-8',
					),
				),
			),
			'stats_labels'      => array(
				't'           => array(
					'name'  => __( 'Tries', 'wp-club-manager' ),
					'label' => _x( 'TR', 'Tries', 'wp-club-manager' ),
				),
				'c'           => array(
					'name'  => __( 'Conversions', 'wp-club-manager' ),
					'label' => _x( 'CON', 'Conversions', 'wp-club-manager' ),
				),
				'p'           => array(
					'name'  => __( 'Penalties', 'wp-club-manager' ),
					'label' => _x( 'PEN', 'Penalties', 'wp-club-manager' ),
				),
				'dg'          => array(
					'name'  => __( 'Drop Goals', 'wp-club-manager' ),
					'label' => _x( 'DG', 'Drop Goals', 'wp-club-manager' ),
				),
				'yellowcards' => array(
					'name'  => __( 'Yellow Cards', 'wp-club-manager' ),
					'label' => _x( 'YC', 'Yellow Cards', 'wp-club-manager' ),
				),
				'redcards'    => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Points For', 'wp-club-manager' ),
					'label' => _x( 'PF', 'Points For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Points Against', 'wp-club-manager' ),
					'label' => _x( 'PA', 'Points Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Points Difference', 'wp-club-manager' ),
					'label' => _x( 'PD', 'Points Difference', 'wp-club-manager' ),
				),
				'b'   => array(
					'name'  => __( 'Bonus Points', 'wp-club-manager' ),
					'label' => _x( 'B', 'Bonus Points', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'soccer'       => array(
			'name'              => __( 'Football (Soccer)', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Defender',
						'slug' => 'defender',
					),
					array(
						'name' => 'Midfielder',
						'slug' => 'midfielder',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels'      => array(
				'goals'       => array(
					'name'  => __( 'Goals', 'wp-club-manager' ),
					'label' => _x( 'GLS', 'Goals', 'wp-club-manager' ),
				),
				'assists'     => array(
					'name'  => __( 'Assists', 'wp-club-manager' ),
					'label' => _x( 'AST', 'Assists', 'wp-club-manager' ),
				),
				'penalties'   => array(
					'name'  => __( 'Penalties', 'wp-club-manager' ),
					'label' => _x( 'PENS', 'Penalties', 'wp-club-manager' ),
				),
				'og'          => array(
					'name'  => __( 'Own Goals', 'wp-club-manager' ),
					'label' => _x( 'OG', 'Own Goals', 'wp-club-manager' ),
				),
				'cs'          => array(
					'name'  => __( 'Clean Sheets', 'wp-club-manager' ),
					'label' => _x( 'CS', 'Clean Sheets', 'wp-club-manager' ),
				),
				'yellowcards' => array(
					'name'  => __( 'Yellow Cards', 'wp-club-manager' ),
					'label' => _x( 'YC', 'Yellow Cards', 'wp-club-manager' ),
				),
				'redcards'    => array(
					'name'  => __( 'Red Cards', 'wp-club-manager' ),
					'label' => _x( 'RC', 'Red Cards', 'wp-club-manager' ),
				),
				'rating'      => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'         => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'd'   => array(
					'name'  => __( 'Draw', 'wp-club-manager' ),
					'label' => _x( 'D', 'Draw', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Goals For', 'wp-club-manager' ),
					'label' => _x( 'F', 'Goals For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Goals Against', 'wp-club-manager' ),
					'label' => _x( 'A', 'Played', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Goal Difference', 'wp-club-manager' ),
					'label' => _x( 'GD', 'Goal Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
		'volleyball'   => array(
			'name'              => __( 'Volleyball', 'wp-club-manager' ),
			'terms'             => array(
				'wpcm_position' => array(
					array(
						'name' => 'Outside Hitter',
						'slug' => 'outside-hitter',
					),
					array(
						'name' => 'Middle Blocker',
						'slug' => 'middle-blocker',
					),
					array(
						'name' => 'Setter',
						'slug' => 'setter',
					),
					array(
						'name' => 'Opposite',
						'slug' => 'opposite',
					),
					array(
						'name' => 'Defensive Specialist',
						'slug' => 'defensive-specialist',
					),
					array(
						'name' => 'Libero',
						'slug' => 'libero',
					),
				),
			),
			'stats_labels'      => array(
				'ace'    => array(
					'name'  => __( 'Aces', 'wp-club-manager' ),
					'label' => _x( 'ACE', 'Aces', 'wp-club-manager' ),
				),
				'kill'   => array(
					'name'  => __( 'Kills', 'wp-club-manager' ),
					'label' => _x( 'KILL', 'Kills', 'wp-club-manager' ),
				),
				'blk'    => array(
					'name'  => __( 'Blocks', 'wp-club-manager' ),
					'label' => _x( 'BLK', 'Blocks', 'wp-club-manager' ),
				),
				'bass'   => array(
					'name'  => __( 'Block Assists', 'wp-club-manager' ),
					'label' => _x( 'BA', 'Block Assists', 'wp-club-manager' ),
				),
				'sass'   => array(
					'name'  => __( 'Setting Assists', 'wp-club-manager' ),
					'label' => _x( 'SA', 'Setting Assists', 'wp-club-manager' ),
				),
				'dig'    => array(
					'name'  => __( 'Digs', 'wp-club-manager' ),
					'label' => _x( 'DIG', 'Digs', 'wp-club-manager' ),
				),
				'rating' => array(
					'name'  => __( 'Rating', 'wp-club-manager' ),
					'label' => _x( 'RAT', 'Rating', 'wp-club-manager' ),
				),
				'mvp'    => array(
					'name'  => __( 'Player of Match', 'wp-club-manager' ),
					'label' => _x( 'POM', 'Player of Match', 'wp-club-manager' ),
				),
			),
			'standings_columns' => array(
				'p'   => array(
					'name'  => __( 'Played', 'wp-club-manager' ),
					'label' => _x( 'P', 'Played', 'wp-club-manager' ),
				),
				'w'   => array(
					'name'  => __( 'Won', 'wp-club-manager' ),
					'label' => _x( 'W', 'Won', 'wp-club-manager' ),
				),
				'l'   => array(
					'name'  => __( 'Lost', 'wp-club-manager' ),
					'label' => _x( 'L', 'Lost', 'wp-club-manager' ),
				),
				'f'   => array(
					'name'  => __( 'Sets For', 'wp-club-manager' ),
					'label' => _x( 'SF', 'Sets For', 'wp-club-manager' ),
				),
				'a'   => array(
					'name'  => __( 'Sets Against', 'wp-club-manager' ),
					'label' => _x( 'SA', 'Sets Against', 'wp-club-manager' ),
				),
				'gd'  => array(
					'name'  => __( 'Set Difference', 'wp-club-manager' ),
					'label' => _x( 'SD', 'Set Difference', 'wp-club-manager' ),
				),
				'pts' => array(
					'name'  => __( 'Points', 'wp-club-manager' ),
					'label' => _x( 'Pts', 'Points', 'wp-club-manager' ),
				),
			),
		),
	));
}

/**
 * @return array
 */
function wpcm_get_sport_options() {
	$sports  = wpcm_get_sport_presets();
	$options = array();
	foreach ( $sports as $slug => $data ) :
		$options[ $slug ] = $data['name'];
	endforeach;
	return $options;
}
