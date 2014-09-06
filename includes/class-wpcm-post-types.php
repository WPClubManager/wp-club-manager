<?php
/**
 * Post types
 *
 * Registers post types.
 *
 * @class 		WPCM_Post_Types
 * @version		1.1.1
 * @package		WPClubManager/Classes/
 * @category	Class
 * @author 		ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_filter('the_posts', array( $this, 'show_future_matches') );
	}

	/**
	 * Register WPClubManager taxonomies.
	 */
	public static function register_taxonomies() {

		if ( taxonomy_exists( 'wpcm_comp' ) )
			return;

		do_action( 'wpclubmanager_register_taxonomy' );

		register_taxonomy( 'wpcm_comp',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_comp', array( 'wpcm_club', 'wpcm_table' ) ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_comp', array(
				'hierarchical' => true,
				'labels' => array (
					'name'               => __( 'Competitions', 'wpclubmanager' ),
					'singular_name'      => __( 'Competition', 'wpclubmanager' ),
					'search_items'       => __( 'Search Competitions', 'wpclubmanager' ),
					'all_items'          => __( 'All Competitions', 'wpclubmanager' ),
					'parent_item'        => __( 'Parent Competition', 'wpclubmanager' ),
					'parent_item_colon'  => __( 'Parent Competition:', 'wpclubmanager' ),
					'edit_item'          => __( 'Edit Competition', 'wpclubmanager' ),
					'update_item'        => __( 'Update Competition', 'wpclubmanager' ),
					'add_new_item'       => __( 'Add New Competition', 'wpclubmanager' ),
					'new_item_name'      => __( 'Competition', 'wpclubmanager' )
				),
				'show_in_nav_menus' => false,
				'sort' => true,
				'rewrite' => array( 'slug' => 'competitions' )
			) )
		);

		register_taxonomy( 'wpcm_jobs',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_jobs', array( 'wpcm_staff' ) ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_jobs', array(
				'hierarchical' => true,
				'labels' => array(
					'name'               => __( 'Jobs', 'wpclubmanager' ),
					'singular_name'      => __( 'Job', 'wpclubmanager' ),
					'search_items'       => __( 'Search Jobs', 'wpclubmanager' ),
					'all_items'          => __( 'All Jobs', 'wpclubmanager' ),
					'parent_item'        => __( 'Parent Job', 'wpclubmanager' ),
					'parent_item_colon'  => __( 'Parent Job:', 'wpclubmanager' ),
					'edit_item'          => __( 'Edit Job', 'wpclubmanager' ),
					'update_item'        => __( 'Update Job', 'wpclubmanager' ),
					'add_new_item'       => __( 'Add New Job', 'wpclubmanager' ),
					'new_item_name'      => __( 'New Job Title', 'wpclubmanager' )
				),
				'show_in_nav_menus' => false,
				'sort' => true,
				'rewrite' => array( 'slug' => 'jobs' )
			) )
		);

		register_taxonomy( 'wpcm_position',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_position', array( 'wpcm_player' ) ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_position', array(
				'hierarchical' => true,
				'labels' => array(
					'name'               => __( 'Positions', 'wpclubmanager' ),
					'singular_name'      => __( 'Position', 'wpclubmanager' ),
					'search_items'       => __( 'Search Positions', 'wpclubmanager' ),
					'all_items'          => __( 'All Positions', 'wpclubmanager' ),
					'parent_item'        => __( 'Parent Position', 'wpclubmanager' ),
					'parent_item_colon'  => __( 'Parent Position:', 'wpclubmanager' ),
					'edit_item'          => __( 'Edit Position', 'wpclubmanager' ),
					'update_item'        => __( 'Update Position', 'wpclubmanager' ),
					'add_new_item'       => __( 'Add New Position', 'wpclubmanager' ),
					'new_item_name'      => __( 'New Position Name', 'wpclubmanager' )
				),
				'show_in_nav_menus' => false,
				'sort' => true,
				'rewrite' => array( 'slug' => 'positions' )
			) )
		);

		register_taxonomy( 'wpcm_season',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_season', array('wpcm_club','wpcm_player','wpcm_staff', 'wpcm_table') ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_season', array(
				'hierarchical' => true,
				'labels' => array(
					'name'               => __( 'Seasons', 'wpclubmanager' ),
					'singular_name'      => __( 'Season', 'wpclubmanager' ),
					'search_items'       => __( 'Search Seasons', 'wpclubmanager' ),
					'all_items'          => __( 'All Seasons', 'wpclubmanager' ),
					'parent_item'        => __( 'Parent Season', 'wpclubmanager' ),
					'parent_item_colon'  => __( 'Parent Season:', 'wpclubmanager' ),
					'edit_item'          => __( 'Edit Season', 'wpclubmanager' ),
					'update_item'        => __( 'Update Season', 'wpclubmanager' ),
					'add_new_item'       => __( 'Add New Season', 'wpclubmanager' ),
					'new_item_name'      => __( 'Season', 'wpclubmanager' )
				),
				'show_in_nav_menus' => false,
				'sort' => true,
				'rewrite' => array( 'slug' => 'seasons' )
			) )
		);

		register_taxonomy( 'wpcm_team',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_team', array('wpcm_club','wpcm_player','wpcm_staff') ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_team', array(
				'hierarchical' =>true,
				'labels' => array(
					'name'               => __( 'Teams', 'wpclubmanager' ),
					'singular_name'      => __( 'Team', 'wpclubmanager' ),
					'search_items'       =>  __( 'Search Teams', 'wpclubmanager' ),
					'all_items'          => __( 'All Teams', 'wpclubmanager' ),
					'parent_item'        => __( 'Parent Team', 'wpclubmanager' ),
					'parent_item_colon'  => __( 'Parent Team:', 'wpclubmanager' ),
					'edit_item'          => __( 'Edit Team', 'wpclubmanager' ),
					'update_item'        => __( 'Update Team', 'wpclubmanager' ),
					'add_new_item'       => __( 'Add New Team', 'wpclubmanager' ),
					'new_item_name'      => __( 'Team', 'wpclubmanager' )
				),
				'show_in_nav_menus' => false,
				'sort' => true,
				'rewrite' => array( 'slug' => 'teams' )
			) )
		);

		register_taxonomy( 'wpcm_venue',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_venue', array( 'wpcm_club' ) ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_venue', array(
				'hierarchical' => true,
				'labels' => array(
					'name'               => __( 'Venues', 'wpclubmanager' ),
					'singular_name'      => __( 'Venue', 'wpclubmanager' ),
					'search_items'       =>  __( 'Search Venues', 'wpclubmanager' ),
					'all_items'          => __( 'All Venues', 'wpclubmanager' ),
					'parent_item'        => __( 'Parent Venue', 'wpclubmanager' ),
					'parent_item_colon'  => __( 'Parent Venue:', 'wpclubmanager' ),
					'edit_item'          => __( 'Edit Venue', 'wpclubmanager' ),
					'update_item'        => __( 'Update Venue', 'wpclubmanager' ),
					'add_new_item'       => __( 'Add New Venue', 'wpclubmanager' ),
					'new_item_name'      => __( 'Venue', 'wpclubmanager' )
				),
				'show_in_nav_menus' => false,
				'sort' => true,
				'rewrite' => array( 'slug' => 'venues' )
			) )
		);

	}

	/**
	 * Register core post types
	 */
	public static function register_post_types() {

		if ( post_type_exists('wpcm_player') )
			return;

		do_action( 'wpclubmanager_register_post_type' );

		register_post_type( 'wpcm_club',
			apply_filters( 'wpclubmanager_register_post_type_club',
				array(
					'labels' => array(
						'name'                => __( 'Clubs', 'wpclubmanager' ),
						'singular_name'       => __( 'Club', 'wpclubmanager' ),
						'add_new'             => __( 'Add New', 'wpclubmanager' ),
						'all_items'           => __( 'All Clubs', 'wpclubmanager' ),
						'add_new_item'        => __( 'Add New Club', 'wpclubmanager' ),
						'edit_item'           => __( 'Edit Club', 'wpclubmanager' ),
						'new_item'            => __( 'New Club', 'wpclubmanager' ),
						'view_item'           => __( 'View Club', 'wpclubmanager' ),
						'search_items'        => __( 'Search Clubs', 'wpclubmanager' ),
						'not_found'           => __( 'No clubs found', 'wpclubmanager' ),
						'not_found_in_trash'  => __( 'No clubs found in trash'),
						'parent_item_colon'   => __( 'Parent Club:', 'wpclubmanager' ),
						'menu_name'           => __( 'Clubs', 'wpclubmanager' )
					),
					'hierarchical'         => false,
					'supports'             => array( 'title', 'editor', 'thumbnail' ),
					'public'               => true,
					'show_ui'              => true,
					'show_in_menu'         => true,
					'show_in_nav_menus'    => false,
					'menu_icon'            => 'dashicons-shield',
					'publicly_queryable'   => true,
					'exclude_from_search'  => true,
					'has_archive'          => false,
					'query_var'            => true,
					'can_export'           => true,
					'rewrite'              => array( 'slug' => 'club' ),
					'capability_type'      => 'post'
				)
			)
		);
		
		register_post_type( 'wpcm_player',
			apply_filters( 'wpclubmanager_register_post_type_player',
				array(
					'labels' => array(
						'name'                => __( 'Players', 'wpclubmanager' ),
						'singular_name'       => __( 'Player', 'wpclubmanager' ),
						'add_new'             => __( 'Add New', 'wpclubmanager' ),
						'all_items'           => __( 'All Players', 'wpclubmanager' ),
						'add_new_item'        => __( 'Add New Player', 'wpclubmanager' ),
						'edit_item'           => __( 'Edit Player', 'wpclubmanager' ),
						'new_item'            => __( 'New Player', 'wpclubmanager' ),
						'view_item'           => __( 'View Player', 'wpclubmanager' ),
						'search_items'        => __( 'Search Players', 'wpclubmanager' ),
						'not_found'           => __( 'No players found', 'wpclubmanager' ),
						'not_found_in_trash'  => __( 'No players found in trash'),
						'parent_item_colon'   => __( 'Parent Player:', 'wpclubmanager' ),
						'menu_name'           => __( 'Players', 'wpclubmanager' )
					),
					'hierarchical'         => false,
					'supports'             => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
					'public'               => true,
					'show_ui'              => true,
					'show_in_menu'         => true,
					'show_in_nav_menus'    => true,
					'menu_icon'            => 'dashicons-groups',
					'publicly_queryable'   => true,
					'exclude_from_search'  => false,
					'has_archive'          => false,
					'query_var'            => true,
					'can_export'           => true,
					'rewrite'              => array( 'slug' => 'player' ),
					'capability_type'      => 'post'
				)
			)
		);

		register_post_type( 'wpcm_staff',
			apply_filters( 'wpclubmanager_register_post_type_staff',
				array(
					'labels' => array(
						'name'                => __( 'Staff', 'wpclubmanager' ),
						'singular_name'       => __( 'Staff', 'wpclubmanager' ),
						'add_new'             => __( 'Add New', 'wpclubmanager' ),
						'all_items'           => __( 'All Staff', 'wpclubmanager' ),
						'add_new_item'        => __( 'Add New Staff', 'wpclubmanager' ),
						'edit_item'           => __( 'Edit Staff', 'wpclubmanager' ),
						'new_item'            => __( 'New Staff', 'wpclubmanager' ),
						'view_item'           => __( 'View Staff', 'wpclubmanager' ),
						'search_items'        => __( 'Search Staff', 'wpclubmanager' ),
						'not_found'           => __( 'No staff found', 'wpclubmanager' ),
						'not_found_in_trash'  => __( 'No staff found in trash'),
						'parent_item_colon'   => __( 'Parent Staff:', 'wpclubmanager' ),
						'menu_name'           => __( 'Staff', 'wpclubmanager' )
					),
					'hierarchical'         => false,
					'supports'             => array( 'title', 'editor', 'thumbnail' ),
					'public'               => true,
					'show_ui'              => true,
					'show_in_menu'         => true,
					'show_in_nav_menus'    => false,
					'menu_icon'            => 'dashicons-businessman',
					'publicly_queryable'   => true,
					'exclude_from_search'  => true,
					'has_archive'          => false,
					'query_var'            => true,
					'can_export'           => true,
					'rewrite'              => array( 'slug' => 'staff' ),
					'capability_type'      => 'post'
				)
			)
		);

		register_post_type( 'wpcm_match',
			apply_filters( 'wpclubmanager_register_post_type_match',
				array(
					'labels' => array(
						'name'                => __( 'Matches', 'wpclubmanager' ),
						'singular_name'       => __( 'Match', 'wpclubmanager' ),
						'add_new'             => __( 'Add New', 'wpclubmanager' ),
						'all_items'           => __( 'All Matches', 'wpclubmanager' ),
						'add_new_item'        => __( 'Add New Match', 'wpclubmanager' ),
						'edit_item'           => __( 'Edit Match', 'wpclubmanager' ),
						'new_item'            => __( 'New Match', 'wpclubmanager' ),
						'view_item'           => __( 'View Match', 'wpclubmanager' ),
						'search_items'        => __( 'Search Matches', 'wpclubmanager' ),
						'not_found'           => __( 'No matches found', 'wpclubmanager' ),
						'not_found_in_trash'  => __( 'No matches found in trash'),
						'parent_item_colon'   => __( 'Parent Match:', 'wpclubmanager' ),
						'menu_name'           => __( 'Matches', 'wpclubmanager' )
					),
					'hierarchical'         => false,
					'supports'             => array( 'editor' ),
					'public'               => true,
					'show_ui'              => true,
					'show_in_menu'         => true,
					'show_in_nav_menus'    => true,
					'menu_icon'            => 'dashicons-awards',
					'publicly_queryable'   => true,
					'exclude_from_search'  => false,
					'has_archive'          => false,
					'query_var'            => true,
					'can_export'           => true,
					'rewrite'              => array( 'slug' => 'match' ),
					'capability_type'      => 'post'
				)
			)
		);

		register_post_type( 'wpcm_sponsor',
			apply_filters( 'wpclubmanager_register_post_type_sponsor',
				array(
					'labels' => array(
						'name'                => __( 'Sponsors', 'wpclubmanager' ),
						'singular_name'       => __( 'Sponsor', 'wpclubmanager' ),
						'add_new'             => __( 'Add New', 'wpclubmanager' ),
						'all_items'           => __( 'All Sponsors', 'wpclubmanager' ),
						'add_new_item'        => __( 'Add New Sponsor', 'wpclubmanager' ),
						'edit_item'           => __( 'Edit Sponsor', 'wpclubmanager' ),
						'new_item'            => __( 'New Sponsor', 'wpclubmanager' ),
						'view_item'           => __( 'View Sponsor', 'wpclubmanager' ),
						'search_items'        => __( 'Search Sponsors', 'wpclubmanager' ),
						'not_found'           => __( 'No sponsors found', 'wpclubmanager' ),
						'not_found_in_trash'  => __( 'No sponsors found in trash'),
						'parent_item_colon'   => __( 'Parent Sponsor:', 'wpclubmanager' ),
						'menu_name'           => __( 'Sponsors', 'wpclubmanager' )
					),
					'hierarchical'         => false,
					'supports'             => array( 'title', 'editor', 'thumbnail' ),
					'public'               => true,
					'show_ui'              => true,
					'show_in_menu'         => true,
					'show_in_nav_menus'    => true,
					'menu_icon'            => 'dashicons-megaphone',
					'publicly_queryable'   => true,
					'exclude_from_search'  => false,
					'has_archive'          => false,
					'query_var'            => true,
					'can_export'           => true,
					'rewrite'              => array( 'slug' => 'sponsors' ),
					'capability_type'      => 'post'
				)
			)
		);
	}

	/**
	 * Show future matches
	 *
	 * @access public
	 * @param string $posts
	 * @return string
	 */
	public function show_future_matches($posts) {
		global $wp_query, $wpdb;
		if(is_single() && $wp_query->post_count == 0  && isset( $wp_query->query_vars['wpcm_match'] )) {
			$posts = $wpdb->get_results($wp_query->request);
		}
		return $posts;
	}
}

new WPCM_Post_Types();