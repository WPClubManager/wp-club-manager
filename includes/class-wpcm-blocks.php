<?php
/**
 * WPCM_Blocks class.
 *
 * Registers Gutenberg blocks for all WPCM shortcodes.
 * Each block uses ServerSideRender in the editor and delegates
 * to the existing shortcode output classes for rendering.
 *
 * @class       WPCM_Blocks
 * @version     2.4.0
 * @package     WPClubManager/Classes
 * @category    Class
 * @author      ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPCM_Blocks
 */
class WPCM_Blocks {

	/**
	 * Initialise block registration hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
		add_filter( 'block_categories_all', array( __CLASS__, 'register_category' ), 10, 2 );
	}

	/**
	 * Register the WP Club Manager block category.
	 *
	 * @param array[]  $categories Array of block categories.
	 * @param WP_Post $post       Post being loaded.
	 * @return array[]
	 */
	public static function register_category( $categories, $post = null ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'wp-club-manager',
					'title' => __( 'WP Club Manager', 'wp-club-manager' ),
					'icon'  => 'awards',
				),
			)
		);
	}

	/**
	 * Register the editor script and all blocks.
	 */
	public static function register_blocks() {
		wp_register_script(
			'wpcm-blocks-editor',
			plugins_url( 'assets/js/blocks/wpcm-blocks-editor.js', WPCM_PLUGIN_FILE ),
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
			WPCM_VERSION,
			true
		);

		$is_club_mode = function_exists( 'is_club_mode' ) && is_club_mode();

		wp_localize_script( 'wpcm-blocks-editor', 'wpcmBlocksConfig', array(
			'clubMode' => $is_club_mode,
		) );

		self::register_match_list();
		self::register_player_list();
		self::register_player_gallery();
		self::register_staff_list();
		self::register_staff_gallery();
		self::register_league_table();
		self::register_map_venue();

		if ( function_exists( 'is_club_mode' ) && is_club_mode() ) {
			self::register_match_opponents();
		}
	}

	/**
	 * Register the Match List block.
	 */
	private static function register_match_list() {
		register_block_type( 'wpcm/match-list', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_match_list' ),
			'attributes'      => array(
				'title'      => array( 'type' => 'string', 'default' => '' ),
				'format'     => array( 'type' => 'string', 'default' => '' ),
				'limit'      => array( 'type' => 'string', 'default' => '' ),
				'comp'       => array( 'type' => 'string', 'default' => '' ),
				'season'     => array( 'type' => 'string', 'default' => '' ),
				'team'       => array( 'type' => 'string', 'default' => '' ),
				'venue'      => array( 'type' => 'string', 'default' => '' ),
				'date_range' => array( 'type' => 'string', 'default' => '' ),
				'order'      => array( 'type' => 'string', 'default' => '' ),
				'show_abbr'  => array( 'type' => 'string', 'default' => '0' ),
				'show_thumb' => array( 'type' => 'string', 'default' => '0' ),
				'show_comp'  => array( 'type' => 'string', 'default' => '1' ),
				'show_team'  => array( 'type' => 'string', 'default' => '0' ),
				'show_venue' => array( 'type' => 'string', 'default' => '0' ),
				'linktext'   => array( 'type' => 'string', 'default' => '' ),
				'linkpage'   => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Register the Player List block.
	 */
	private static function register_player_list() {
		register_block_type( 'wpcm/player-list', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_player_list' ),
			'attributes'      => array(
				'id'          => array( 'type' => 'string', 'default' => '' ),
				'limit'       => array( 'type' => 'string', 'default' => '' ),
				'position'    => array( 'type' => 'string', 'default' => '' ),
				'orderby'     => array( 'type' => 'string', 'default' => 'number' ),
				'order'       => array( 'type' => 'string', 'default' => 'ASC' ),
				'linktext'    => array( 'type' => 'string', 'default' => '' ),
				'linkpage'    => array( 'type' => 'string', 'default' => '' ),
				'columns'     => array( 'type' => 'string', 'default' => '' ),
				'title'       => array( 'type' => 'string', 'default' => '' ),
				'name_format' => array( 'type' => 'string', 'default' => 'full' ),
				'type'        => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Register the Player Gallery block.
	 */
	private static function register_player_gallery() {
		register_block_type( 'wpcm/player-gallery', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_player_gallery' ),
			'attributes'      => array(
				'id'          => array( 'type' => 'string', 'default' => '' ),
				'title'       => array( 'type' => 'string', 'default' => '' ),
				'limit'       => array( 'type' => 'string', 'default' => '' ),
				'position'    => array( 'type' => 'string', 'default' => '' ),
				'orderby'     => array( 'type' => 'string', 'default' => 'number' ),
				'order'       => array( 'type' => 'string', 'default' => 'ASC' ),
				'columns'     => array( 'type' => 'string', 'default' => '3' ),
				'linktext'    => array( 'type' => 'string', 'default' => '' ),
				'linkpage'    => array( 'type' => 'string', 'default' => '' ),
				'name_format' => array( 'type' => 'string', 'default' => 'full' ),
				'type'        => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Register the Staff List block.
	 */
	private static function register_staff_list() {
		register_block_type( 'wpcm/staff-list', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_staff_list' ),
			'attributes'      => array(
				'id'          => array( 'type' => 'string', 'default' => '' ),
				'limit'       => array( 'type' => 'string', 'default' => '' ),
				'job'         => array( 'type' => 'string', 'default' => '' ),
				'orderby'     => array( 'type' => 'string', 'default' => 'name' ),
				'order'       => array( 'type' => 'string', 'default' => 'ASC' ),
				'linktext'    => array( 'type' => 'string', 'default' => '' ),
				'linkpage'    => array( 'type' => 'string', 'default' => '' ),
				'columns'     => array( 'type' => 'string', 'default' => '' ),
				'title'       => array( 'type' => 'string', 'default' => '' ),
				'name_format' => array( 'type' => 'string', 'default' => 'full' ),
				'type'        => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Register the Staff Gallery block.
	 */
	private static function register_staff_gallery() {
		register_block_type( 'wpcm/staff-gallery', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_staff_gallery' ),
			'attributes'      => array(
				'id'          => array( 'type' => 'string', 'default' => '' ),
				'title'       => array( 'type' => 'string', 'default' => '' ),
				'limit'       => array( 'type' => 'string', 'default' => '' ),
				'jobs'        => array( 'type' => 'string', 'default' => '' ),
				'orderby'     => array( 'type' => 'string', 'default' => 'name' ),
				'order'       => array( 'type' => 'string', 'default' => 'ASC' ),
				'columns'     => array( 'type' => 'string', 'default' => '3' ),
				'linktext'    => array( 'type' => 'string', 'default' => '' ),
				'linkpage'    => array( 'type' => 'string', 'default' => '' ),
				'name_format' => array( 'type' => 'string', 'default' => 'full' ),
				'type'        => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Register the League Table block.
	 */
	private static function register_league_table() {
		register_block_type( 'wpcm/league-table', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_league_table' ),
			'attributes'      => array(
				'id'        => array( 'type' => 'string', 'default' => '' ),
				'title'     => array( 'type' => 'string', 'default' => '' ),
				'limit'     => array( 'type' => 'string', 'default' => '' ),
				'focus'     => array( 'type' => 'string', 'default' => '' ),
				'abbr'      => array( 'type' => 'string', 'default' => '0' ),
				'thumb'     => array( 'type' => 'string', 'default' => '1' ),
				'link_club' => array( 'type' => 'string', 'default' => '1' ),
				'type'      => array( 'type' => 'string', 'default' => '' ),
				'notes'     => array( 'type' => 'string', 'default' => '0' ),
				'columns'   => array( 'type' => 'string', 'default' => '' ),
				'linktext'  => array( 'type' => 'string', 'default' => '' ),
				'linkpage'  => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Register the Map Venue block.
	 */
	private static function register_map_venue() {
		register_block_type( 'wpcm/map-venue', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_map_venue' ),
			'attributes'      => array(
				'id'     => array( 'type' => 'string', 'default' => '' ),
				'title'  => array( 'type' => 'string', 'default' => '' ),
				'width'  => array( 'type' => 'string', 'default' => '' ),
				'height' => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Register the Match Opponents block (club mode only).
	 */
	private static function register_match_opponents() {
		register_block_type( 'wpcm/match-opponents', array(
			'editor_script'   => 'wpcm-blocks-editor',
			'render_callback' => array( __CLASS__, 'render_match_opponents' ),
			'attributes'      => array(
				'title'      => array( 'type' => 'string', 'default' => '' ),
				'format'     => array( 'type' => 'string', 'default' => '' ),
				'id'         => array( 'type' => 'string', 'default' => '' ),
				'limit'      => array( 'type' => 'string', 'default' => '' ),
				'comp'       => array( 'type' => 'string', 'default' => '' ),
				'season'     => array( 'type' => 'string', 'default' => '' ),
				'team'       => array( 'type' => 'string', 'default' => '' ),
				'date_range' => array( 'type' => 'string', 'default' => '' ),
				'venue'      => array( 'type' => 'string', 'default' => '' ),
				'order'      => array( 'type' => 'string', 'default' => '' ),
				'show_abbr'  => array( 'type' => 'string', 'default' => '0' ),
				'show_thumb' => array( 'type' => 'string', 'default' => '0' ),
				'show_team'  => array( 'type' => 'string', 'default' => '0' ),
				'show_comp'  => array( 'type' => 'string', 'default' => '1' ),
				'show_venue' => array( 'type' => 'string', 'default' => '1' ),
				'linktext'   => array( 'type' => 'string', 'default' => '' ),
				'linkpage'   => array( 'type' => 'string', 'default' => '' ),
			),
		) );
	}

	/**
	 * Render the Match List block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_match_list( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_Match_List', 'output' ),
			$attributes
		);
	}

	/**
	 * Render the Player List block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_player_list( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_Player_List', 'output' ),
			$attributes
		);
	}

	/**
	 * Render the Player Gallery block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_player_gallery( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_Player_Gallery', 'output' ),
			$attributes
		);
	}

	/**
	 * Render the Staff List block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_staff_list( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_Staff_List', 'output' ),
			$attributes
		);
	}

	/**
	 * Render the Staff Gallery block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_staff_gallery( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_Staff_Gallery', 'output' ),
			$attributes
		);
	}

	/**
	 * Render the League Table block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_league_table( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_League_Table', 'output' ),
			$attributes
		);
	}

	/**
	 * Render the Map Venue block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_map_venue( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_Map_Venue', 'output' ),
			$attributes
		);
	}

	/**
	 * Render the Match Opponents block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_match_opponents( $attributes ) {
		return WPCM_Shortcodes::shortcode_wrapper(
			array( 'WPCM_Shortcode_Match_Opponents', 'output' ),
			$attributes
		);
	}
}
