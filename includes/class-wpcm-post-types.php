<?php
/**
 * Post types
 *
 * Registers post types.
 *
 * @class       WPCM_Post_Types
 * @version     2.1.9
 * @package     WPClubManager/Classes/
 * @category    Class
 * @author      ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Post_Types
 */
class WPCM_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'the_posts', array( __CLASS__, 'show_future_matches' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}

	/**
	 * Register WPClubManager taxonomies.
	 */
	public static function register_taxonomies() {

		if ( taxonomy_exists( 'wpcm_comp' ) ) {
			return;
		}

		do_action( 'wpclubmanager_register_taxonomy' );

		register_taxonomy( 'wpcm_comp',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_comp', null ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_comp', array(
				'hierarchical'       => true,
				'labels'             => array(
					'name'                     => __( 'Competitions', 'wp-club-manager' ),
					'singular_name'            => __( 'Competition', 'wp-club-manager' ),
					'search_items'             => __( 'Search Competitions', 'wp-club-manager' ),
					'all_items'                => __( 'All Competitions', 'wp-club-manager' ),
					'parent_item'              => __( 'Parent Competition', 'wp-club-manager' ),
					'parent_item_colon'        => __( 'Parent Competition:', 'wp-club-manager' ),
					'edit_item'                => __( 'Edit Competition', 'wp-club-manager' ),
					'update_item'              => __( 'Update Competition', 'wp-club-manager' ),
					'add_new_item'             => __( 'Add New Competition', 'wp-club-manager' ),
					'new_item_name'            => __( 'Competition', 'wp-club-manager' ),
					'name_field_description'   => __( 'The name is how it appears on your site. You can create a shorter version of the competition name in the label field below.', 'wp-club-manager' ),
					'parent_field_description' => __( 'You can assign a parent competition to create a hierarchy. For example, Eastern Conference could be the parent of Division One.', 'wp-club-manager' ),
				),
				'show_in_nav_menus'  => false,
				'tax_position'       => true,
				'capabilities'       => array(
					'manage_terms' => 'manage_wpcm_club_terms',
					'edit_terms'   => 'edit_wpcm_club_terms',
					'delete_terms' => 'delete_wpcm_club_terms',
					'assign_terms' => 'assign_wpcm_club_terms',
				),
				'publicly_queryable' => false,
				'show_in_rest'       => true,
			) )
		);

		register_taxonomy( 'wpcm_jobs',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_jobs', array( 'wpcm_staff' ) ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_jobs', array(
				'hierarchical'       => true,
				'labels'             => array(
					'name'                     => __( 'Jobs', 'wp-club-manager' ),
					'singular_name'            => __( 'Job', 'wp-club-manager' ),
					'search_items'             => __( 'Search Jobs', 'wp-club-manager' ),
					'all_items'                => __( 'All Jobs', 'wp-club-manager' ),
					'parent_item'              => __( 'Parent Job', 'wp-club-manager' ),
					'parent_item_colon'        => __( 'Parent Job:', 'wp-club-manager' ),
					'edit_item'                => __( 'Edit Job', 'wp-club-manager' ),
					'update_item'              => __( 'Update Job', 'wp-club-manager' ),
					'add_new_item'             => __( 'Add New Job', 'wp-club-manager' ),
					'new_item_name'            => __( 'New Job Title', 'wp-club-manager' ),
					'parent_field_description' => __( 'You can assign a parent job to create a hierarchy.', 'wp-club-manager' ),
				),
				'show_in_nav_menus'  => false,
				'tax_position'       => true,
				'capabilities'       => array(
					'manage_terms' => 'manage_wpcm_staff_terms',
					'edit_terms'   => 'edit_wpcm_staff_terms',
					'delete_terms' => 'delete_wpcm_staff_terms',
					'assign_terms' => 'assign_wpcm_staff_terms',
				),
				'publicly_queryable' => false,
				'show_in_rest'       => true,
			) )
		);

		register_taxonomy( 'wpcm_position',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_position', array( 'wpcm_player' ) ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_position', array(
				'hierarchical'       => true,
				'labels'             => array(
					'name'                     => __( 'Positions', 'wp-club-manager' ),
					'singular_name'            => __( 'Position', 'wp-club-manager' ),
					'search_items'             => __( 'Search Positions', 'wp-club-manager' ),
					'all_items'                => __( 'All Positions', 'wp-club-manager' ),
					'parent_item'              => __( 'Parent Position', 'wp-club-manager' ),
					'parent_item_colon'        => __( 'Parent Position:', 'wp-club-manager' ),
					'edit_item'                => __( 'Edit Position', 'wp-club-manager' ),
					'update_item'              => __( 'Update Position', 'wp-club-manager' ),
					'add_new_item'             => __( 'Add New Position', 'wp-club-manager' ),
					'new_item_name'            => __( 'New Position Name', 'wp-club-manager' ),
					'parent_field_description' => __( 'You can assign a parent position to create a hierarchy. For example, Defense could be the parent of Linebacker', 'wp-club-manager' ),
				),
				'show_in_nav_menus'  => false,
				'tax_position'       => true,
				'capabilities'       => array(
					'manage_terms' => 'manage_wpcm_player_terms',
					'edit_terms'   => 'edit_wpcm_player_terms',
					'delete_terms' => 'delete_wpcm_player_terms',
					'assign_terms' => 'assign_wpcm_player_terms',
				),
				'publicly_queryable' => false,
				'show_in_rest'       => true,
			) )
		);

		register_taxonomy( 'wpcm_season',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_season', array( 'wpcm_player', 'wpcm_staff', 'wpcm_match' ) ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_season', array(
				'hierarchical'       => true,
				'labels'             => array(
					'name'                     => __( 'Seasons', 'wp-club-manager' ),
					'singular_name'            => __( 'Season', 'wp-club-manager' ),
					'search_items'             => __( 'Search Seasons', 'wp-club-manager' ),
					'all_items'                => __( 'All Seasons', 'wp-club-manager' ),
					'parent_item'              => __( 'Parent Season', 'wp-club-manager' ),
					'parent_item_colon'        => __( 'Parent Season:', 'wp-club-manager' ),
					'edit_item'                => __( 'Edit Season', 'wp-club-manager' ),
					'update_item'              => __( 'Update Season', 'wp-club-manager' ),
					'add_new_item'             => __( 'Add New Season', 'wp-club-manager' ),
					'new_item_name'            => __( 'Season', 'wp-club-manager' ),
					'name_field_description'   => __( 'The name is how it appears on your site.', 'wp-club-manager' ),
					'parent_field_description' => __( 'You can assign a parent season to create a hierarchy. For example, 2022 could be a parent of Summer.', 'wp-club-manager' ),
				),
				'show_in_nav_menus'  => false,
				'tax_position'       => true,
				'capabilities'       => array(
					'manage_terms' => 'manage_wpcm_club_terms',
					'edit_terms'   => 'edit_wpcm_club_terms',
					'delete_terms' => 'delete_wpcm_club_terms',
					'assign_terms' => 'assign_wpcm_club_terms',
				),
				'publicly_queryable' => false,
				'show_in_rest'       => true,
			) )
		);

		if ( is_club_mode() ) {
			register_taxonomy( 'wpcm_team',
				apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_team', array( 'wpcm_player', 'wpcm_staff', 'wpcm_table' ) ),
				apply_filters( 'wpclubmanager_taxonomy_args_wpcm_team', array(
					'hierarchical'       => true,
					'labels'             => array(
						'name'                     => __( 'Teams', 'wp-club-manager' ),
						'singular_name'            => __( 'Team', 'wp-club-manager' ),
						'search_items'             => __( 'Search Teams', 'wp-club-manager' ),
						'all_items'                => __( 'All Teams', 'wp-club-manager' ),
						'parent_item'              => __( 'Parent Team', 'wp-club-manager' ),
						'parent_item_colon'        => __( 'Parent Team:', 'wp-club-manager' ),
						'edit_item'                => __( 'Edit Team', 'wp-club-manager' ),
						'update_item'              => __( 'Update Team', 'wp-club-manager' ),
						'add_new_item'             => __( 'Add New Team', 'wp-club-manager' ),
						'new_item_name'            => __( 'Team', 'wp-club-manager' ),
						'name_field_description'   => __( 'The full name of the team, for example, First Team. You can create an alternative version to be displayed on the frontend of your site in the display name field below.', 'wp-club-manager' ),
						'parent_field_description' => __( 'You can assign a parent team to create a hierarchy. For example, Senior could be the parent of First Team.', 'wp-club-manager' ),
					),
					'show_in_nav_menus'  => false,
					'tax_position'       => true,
					'capabilities'       => array(
						'manage_terms' => 'manage_wpcm_player_terms',
						'edit_terms'   => 'edit_wpcm_player_terms',
						'delete_terms' => 'delete_wpcm_player_terms',
						'assign_terms' => 'assign_wpcm_player_terms',
					),
					'publicly_queryable' => false,
					'show_in_rest'       => true,
				) )
			);
		}

		register_taxonomy( 'wpcm_venue',
			apply_filters( 'wpclubmanager_taxonomy_objects_wpcm_venue', null ),
			apply_filters( 'wpclubmanager_taxonomy_args_wpcm_venue', array(
				'hierarchical'       => true,
				'labels'             => array(
					'name'              => __( 'Venues', 'wp-club-manager' ),
					'singular_name'     => __( 'Venue', 'wp-club-manager' ),
					'search_items'      => __( 'Search Venues', 'wp-club-manager' ),
					'all_items'         => __( 'All Venues', 'wp-club-manager' ),
					'parent_item'       => __( 'Parent Venue', 'wp-club-manager' ),
					'parent_item_colon' => __( 'Parent Venue:', 'wp-club-manager' ),
					'edit_item'         => __( 'Edit Venue', 'wp-club-manager' ),
					'update_item'       => __( 'Update Venue', 'wp-club-manager' ),
					'add_new_item'      => __( 'Add New Venue', 'wp-club-manager' ),
					'new_item_name'     => __( 'Venue', 'wp-club-manager' ),
				),
				'show_in_nav_menus'  => false,
				'capabilities'       => array(
					'manage_terms' => 'manage_wpcm_club_terms',
					'edit_terms'   => 'edit_wpcm_club_terms',
					'delete_terms' => 'delete_wpcm_club_terms',
					'assign_terms' => 'assign_wpcm_club_terms',
				),
				'publicly_queryable' => false,
				'show_in_rest'       => true,
			) )
		);
	}

	/**
	 * Register core post types
	 */
	public static function register_post_types() {

		if ( post_type_exists( 'wpcm_player' ) ) {
			return;
		}

		do_action( 'wpclubmanager_register_post_type' );

		// $permalink      = get_option( 'wpclubmanager_club_slug' );
		// $club_permalink = empty( $permalink ) ? _x( 'club', 'slug', 'wp-club-manager' ) : $permalink;

		register_post_type( 'wpcm_club',
			apply_filters( 'wpclubmanager_register_post_type_club',
				array(
					'labels'              => array(
						'name'               => __( 'Clubs', 'wp-club-manager' ),
						'singular_name'      => __( 'Club', 'wp-club-manager' ),
						'add_new'            => __( 'Add New', 'wp-club-manager' ),
						'all_items'          => __( 'All Clubs', 'wp-club-manager' ),
						'add_new_item'       => __( 'Add New Club', 'wp-club-manager' ),
						'edit_item'          => __( 'Edit Club', 'wp-club-manager' ),
						'new_item'           => __( 'New Club', 'wp-club-manager' ),
						'view_item'          => __( 'View Club', 'wp-club-manager' ),
						'search_items'       => __( 'Search Clubs', 'wp-club-manager' ),
						'not_found'          => __( 'No clubs found', 'wp-club-manager' ),
						'not_found_in_trash' => __( 'No clubs found in trash' ),
						'parent_item_colon'  => __( 'Parent Club:', 'wp-club-manager' ),
						'menu_name'          => __( 'Clubs', 'wp-club-manager' ),
					),
					'hierarchical'        => true,
					'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => false,
					'menu_icon'           => 'dashicons-shield',
					'publicly_queryable'  => true,
					'exclude_from_search' => true,
					'has_archive'         => false,
					'query_var'           => true,
					'can_export'          => true,
					'rewrite'             => false,
					'capability_type'     => 'wpcm_club',
					'map_meta_cap'        => true,
					'taxonomies'          => array( 'wpcm_venue' ),
					'show_in_rest'        => true,
				)
			)
		);

		$permalink        = get_option( 'wpclubmanager_player_slug' );
		$player_permalink = empty( $permalink ) ? _x( 'player', 'slug', 'wp-club-manager' ) : $permalink;

		register_post_type( 'wpcm_player',
			apply_filters( 'wpclubmanager_register_post_type_player',
				array(
					'labels'              => array(
						'name'               => __( 'Players', 'wp-club-manager' ),
						'singular_name'      => __( 'Player', 'wp-club-manager' ),
						'add_new'            => __( 'Add New', 'wp-club-manager' ),
						'all_items'          => __( 'All Players', 'wp-club-manager' ),
						'add_new_item'       => __( 'Add New Player', 'wp-club-manager' ),
						'edit_item'          => __( 'Edit Player', 'wp-club-manager' ),
						'new_item'           => __( 'New Player', 'wp-club-manager' ),
						'view_item'          => __( 'View Player', 'wp-club-manager' ),
						'search_items'       => __( 'Search Players', 'wp-club-manager' ),
						'not_found'          => __( 'No players found', 'wp-club-manager' ),
						'not_found_in_trash' => __( 'No players found in trash' ),
						'parent_item_colon'  => __( 'Parent Player:', 'wp-club-manager' ),
						'menu_name'          => __( 'Players', 'wp-club-manager' ),
					),
					'hierarchical'        => false,
					'supports'            => array( 'title', 'thumbnail' ),
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'menu_icon'           => 'dashicons-groups',
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'has_archive'         => false,
					'query_var'           => true,
					'can_export'          => true,
					'rewrite'             => $player_permalink ? array( 'slug' => untrailingslashit( $player_permalink ) ) : false,
					'capability_type'     => 'wpcm_player',
					'map_meta_cap'        => true,
					'show_in_rest'        => true,
				)
			)
		);

		$permalink       = get_option( 'wpclubmanager_staff_slug' );
		$staff_permalink = empty( $permalink ) ? _x( 'staff', 'slug', 'wp-club-manager' ) : $permalink;

		register_post_type( 'wpcm_staff',
			apply_filters( 'wpclubmanager_register_post_type_staff',
				array(
					'labels'              => array(
						'name'               => __( 'Staff', 'wp-club-manager' ),
						'singular_name'      => __( 'Staff', 'wp-club-manager' ),
						'add_new'            => __( 'Add New', 'wp-club-manager' ),
						'all_items'          => __( 'All Staff', 'wp-club-manager' ),
						'add_new_item'       => __( 'Add New Staff', 'wp-club-manager' ),
						'edit_item'          => __( 'Edit Staff', 'wp-club-manager' ),
						'new_item'           => __( 'New Staff', 'wp-club-manager' ),
						'view_item'          => __( 'View Staff', 'wp-club-manager' ),
						'search_items'       => __( 'Search Staff', 'wp-club-manager' ),
						'not_found'          => __( 'No staff found', 'wp-club-manager' ),
						'not_found_in_trash' => __( 'No staff found in trash' ),
						'parent_item_colon'  => __( 'Parent Staff:', 'wp-club-manager' ),
						'menu_name'          => __( 'Staff', 'wp-club-manager' ),
					),
					'hierarchical'        => false,
					'supports'            => array( 'title', 'thumbnail' ),
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => false,
					'menu_icon'           => 'dashicons-businessman',
					'publicly_queryable'  => true,
					'exclude_from_search' => true,
					'has_archive'         => false,
					'query_var'           => true,
					'can_export'          => true,
					'rewrite'             => $staff_permalink ? array( 'slug' => untrailingslashit( $staff_permalink ) ) : false,
					'capability_type'     => 'wpcm_staff',
					'map_meta_cap'        => true,
					'show_in_rest'        => true,
				)
			)
		);

		$permalink       = get_option( 'wpclubmanager_match_slug' );
		$match_permalink = empty( $permalink ) ? _x( 'match', 'slug', 'wp-club-manager' ) : $permalink;

		register_post_type( 'wpcm_match',
			apply_filters( 'wpclubmanager_register_post_type_match',
				array(
					'labels'              => array(
						'name'               => __( 'Matches', 'wp-club-manager' ),
						'singular_name'      => __( 'Match', 'wp-club-manager' ),
						'add_new'            => __( 'Add New', 'wp-club-manager' ),
						'all_items'          => __( 'All Matches', 'wp-club-manager' ),
						'add_new_item'       => __( 'Add New Match', 'wp-club-manager' ),
						'edit_item'          => __( 'Edit Match', 'wp-club-manager' ),
						'new_item'           => __( 'New Match', 'wp-club-manager' ),
						'view_item'          => __( 'View Match', 'wp-club-manager' ),
						'search_items'       => __( 'Search Matches', 'wp-club-manager' ),
						'not_found'          => __( 'No matches found', 'wp-club-manager' ),
						'not_found_in_trash' => __( 'No matches found in trash' ),
						'parent_item_colon'  => __( 'Parent Match:', 'wp-club-manager' ),
						'menu_name'          => __( 'Matches', 'wp-club-manager' ),
					),
					'hierarchical'        => false,
					'supports'            => array( 'title' ),
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'menu_icon'           => 'dashicons-calendar-alt',
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'has_archive'         => false,
					'query_var'           => true,
					'can_export'          => true,
					'rewrite'             => $match_permalink ? array( 'slug' => untrailingslashit( $match_permalink ) ) : false,
					'capability_type'     => 'wpcm_match',
					'map_meta_cap'        => true,
					'show_in_rest'        => true,
				)
			)
		);

		register_post_type( 'wpcm_table',
			apply_filters( 'wpclubmanager_register_post_type_table',
				array(
					'labels'            => array(
						'name'               => __( 'League Tables', 'wp-club-manager' ),
						'singular_name'      => __( 'League Table', 'wp-club-manager' ),
						'add_new'            => __( 'Add New', 'wp-club-manager' ),
						'all_items'          => __( 'All League Tables', 'wp-club-manager' ),
						'add_new_item'       => __( 'Add New League Table', 'wp-club-manager' ),
						'edit_item'          => __( 'Edit League Table', 'wp-club-manager' ),
						'new_item'           => __( 'New League Table', 'wp-club-manager' ),
						'view_item'          => __( 'View League Table', 'wp-club-manager' ),
						'search_items'       => __( 'Search League Tables', 'wp-club-manager' ),
						'not_found'          => __( 'No league tables found', 'wp-club-manager' ),
						'not_found_in_trash' => __( 'No league tables found in trash', 'wp-club-manager' ),
						'parent_item_colon'  => __( 'Parent League Table:', 'wp-club-manager' ),
						'menu_name'          => __( 'League Tables', 'wp-club-manager' ),
					),
					'hierarchical'      => false,
					'supports'          => array( 'title' ),
					'public'            => false,
					'show_ui'           => true,
					'show_in_menu'      => false,
					'query_var'         => false,
					'rewrite'           => false,
					'capability_type'   => 'wpcm_table',
					'map_meta_cap'      => true,
					'show_in_admin_bar' => true,
					'taxonomies'        => array( 'wpcm_team', 'wpcm_season', 'wpcm_comp' ),
					'show_in_rest'      => true,
				)
			)
		);

		if ( is_club_mode() ) {
			register_post_type( 'wpcm_roster',
				apply_filters( 'wpclubmanager_register_post_type_roster',
					array(
						'labels'              => array(
							'name'               => __( 'Rosters', 'wp-club-manager' ),
							'singular_name'      => __( 'Roster', 'wp-club-manager' ),
							'add_new'            => __( 'Add New', 'wp-club-manager' ),
							'all_items'          => __( 'All Rosters', 'wp-club-manager' ),
							'add_new_item'       => __( 'Add New Roster', 'wp-club-manager' ),
							'edit_item'          => __( 'Edit Roster', 'wp-club-manager' ),
							'new_item'           => __( 'New Roster', 'wp-club-manager' ),
							'view_item'          => __( 'View Roster', 'wp-club-manager' ),
							'search_items'       => __( 'Search Rosters', 'wp-club-manager' ),
							'not_found'          => __( 'No rosters found', 'wp-club-manager' ),
							'not_found_in_trash' => __( 'No rosters found in trash', 'wp-club-manager' ),
							'parent_item_colon'  => __( 'Parent Roster:', 'wp-club-manager' ),
							'menu_name'          => __( 'Rosters', 'wp-club-manager' ),
						),
						'public'              => false,
						'show_ui'             => true,
						'capability_type'     => 'wpcm_roster',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'show_in_menu'        => false,
						'hierarchical'        => false,
						'rewrite'             => false,
						'query_var'           => false,
						'supports'            => array( 'title' ),
						'show_in_nav_menus'   => false,
						'show_in_admin_bar'   => true,
						'taxonomies'          => array( 'wpcm_team', 'wpcm_season' ),
						'show_in_rest'        => true,
					)
				)
			);
		}

		register_post_type( 'wpcm_sponsor',
			apply_filters( 'wpclubmanager_register_post_type_sponsor',
				array(
					'labels'              => array(
						'name'               => __( 'Sponsors', 'wp-club-manager' ),
						'singular_name'      => __( 'Sponsor', 'wp-club-manager' ),
						'add_new'            => __( 'Add New', 'wp-club-manager' ),
						'all_items'          => __( 'All Sponsors', 'wp-club-manager' ),
						'add_new_item'       => __( 'Add New Sponsor', 'wp-club-manager' ),
						'edit_item'          => __( 'Edit Sponsor', 'wp-club-manager' ),
						'new_item'           => __( 'New Sponsor', 'wp-club-manager' ),
						'view_item'          => __( 'View Sponsor', 'wp-club-manager' ),
						'search_items'       => __( 'Search Sponsors', 'wp-club-manager' ),
						'not_found'          => __( 'No sponsors found', 'wp-club-manager' ),
						'not_found_in_trash' => __( 'No sponsors found in trash' ),
						'parent_item_colon'  => __( 'Parent Sponsor:', 'wp-club-manager' ),
						'menu_name'          => __( 'Sponsors', 'wp-club-manager' ),
					),
					'hierarchical'        => false,
					'supports'            => array( 'title', 'thumbnail' ),
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => false,
					'menu_icon'           => 'dashicons-megaphone',
					'publicly_queryable'  => true,
					'exclude_from_search' => true,
					'has_archive'         => false,
					'query_var'           => true,
					'can_export'          => true,
					'rewrite'             => array(
						'with_front' => false,
						'slug'       => 'sponsors',
					),
					'capability_type'     => 'wpcm_sponsor',
					'map_meta_cap'        => true,
					'show_in_rest'        => true,
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
	public static function show_future_matches( $posts ) {
		global $wp_query, $wpdb;
		if ( is_single() && 0 === $wp_query->post_count && isset( $wp_query->query_vars['wpcm_match'] ) ) {
			$posts = $wpdb->get_results( $wp_query->request ); // phpcs:ignore
		}
		return $posts;
	}

	/**
	 * Add Product Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'wpcm_club' );
			new Jetpack_Omnisearch_Posts( 'wpcm_player' );
			new Jetpack_Omnisearch_Posts( 'wpcm_staff' );
			new Jetpack_Omnisearch_Posts( 'wpcm_match' );
			new Jetpack_Omnisearch_Posts( 'wpcm_roster' );
			new Jetpack_Omnisearch_Posts( 'wpcm_table' );
		}
	}

	/**
	 * Added product for Jetpack related posts.
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'wpcm_player';

		return $post_types;
	}
}

new WPCM_Post_Types();
