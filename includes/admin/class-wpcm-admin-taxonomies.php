<?php
/**
 * Handles taxonomies in admin
 *
 * @class 		WPCM_Admin_Taxonomies
 * @version		1.0.0
 * @package		WPClubManager/Admin
 * @category	Class
 * @author 		ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Admin_Taxonomies {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'wpcm_comp_add_form_fields', array( $this, 'comp_add_new_extra_fields' ), 10, 2 );
		add_action('wpcm_comp_edit_form_fields',array( $this, 'comp_edit_extra_fields' ), 10, 2);

		add_action('edited_wpcm_comp', array( $this, 'save_comp_extra_fields' ), 10, 2);
		add_action( 'create_wpcm_comp', array( $this, 'save_comp_extra_fields' ), 10, 2 );

		add_action('manage_wpcm_comp_custom_column', array( $this, 'comp_custom_columns' ), 5,3);
		add_action('manage_wpcm_venue_custom_column', array( $this, 'venue_custom_columns' ), 5,3);

		add_action( 'wpcm_venue_add_form_fields', array( $this, 'venue_add_new_extra_fields' ), 10, 2 );
		add_action('wpcm_venue_edit_form_fields', array( $this, 'venue_edit_extra_fields' ), 10, 2);

		add_action('edited_wpcm_venue', array( $this, 'save_venue_extra_fields' ), 10, 2);
		add_action( 'create_wpcm_venue', array( $this, 'save_venue_extra_fields' ), 10, 2 );

		add_filter('manage_edit-wpcm_comp_columns', array( $this, 'comp_edit_columns') );
		add_filter('manage_edit-wpcm_season_columns', array( $this, 'season_edit_columns') );
		add_filter('manage_edit-wpcm_team_columns', array( $this, 'team_edit_columns') );
		add_filter('manage_edit-wpcm_venue_columns', array( $this, 'venue_edit_columns') );
	}


	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function comp_add_new_extra_fields( $tag ) { ?>

		<div class="form-field">
			<label for="term_meta[wpcm_comp_label]"><?php _e('Competition Label', 'wpclubmanager'); ?></label>
			<input name="term_meta[wpcm_comp_label]" id="term_meta[wpcm_comp_label]" type="text" value="<?php echo (isset($term_meta['wpcm_comp_label'])&&!empty($term_meta['wpcm_comp_label'])) ? $term_meta['wpcm_comp_label'] : '' ?>"/>
			<p><?php _e('The competition label is used to display a shortened version of the competition name.', 'wpclubmanager'); ?></p>
		</div>
	<?php }

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function comp_edit_extra_fields( $tag ) {

		$t_id = $tag->term_id;
		$term_meta = get_option( "taxonomy_term_$t_id" ); ?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[wpcm_comp_label]"><?php _e('Competition Label', 'wpclubmanager'); ?></label>
			</th>
			<td>
				<input name="term_meta[wpcm_comp_label]" id="term_meta[wpcm_comp_label]" type="text" value="<?php echo $term_meta['wpcm_comp_label'] ? $term_meta['wpcm_comp_label'] : '' ?>"/>
				<p class="description"><?php _e('The competition label is used to display a shortened version of the competition name.', 'wpclubmanager'); ?></p>
			</td>
		</tr>
	<?php }

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function save_comp_extra_fields( $term_id ) {
		
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "taxonomy_term_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ){
				if ( isset( $_POST['term_meta'][$key] ) ){
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			update_option( "taxonomy_term_$t_id", $term_meta );
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function comp_edit_columns($columns) {
		
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"name" => __('Name', 'wpclubmanager'),
			"description" => __('Description', 'wpclubmanager'),
			"label" => __('Label', 'wpclubmanager')
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function comp_custom_columns($value, $column, $t_id) {
		
		global $post;

		$term_meta = get_option( "taxonomy_term_$t_id" );

		switch ($column) {
		case 'label':
			echo $term_meta['wpcm_comp_label'];
			break;
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function season_edit_columns($columns) {

		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"name" => __('Name', 'wpclubmanager'),
			"description" => __('Description', 'wpclubmanager'),
			"slug" => __('Slug', 'wpclubmanager')
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function team_edit_columns($columns) {
		
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"name" => __('Name', 'wpclubmanager'),
			"description" => __('Description', 'wpclubmanager'),
			"slug" => __('Slug', 'wpclubmanager')
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function venue_add_new_extra_fields( $tag ) { ?>

		<div class="form-field">
			<label for="term_meta[wpcm_address]"><?php _e('Venue Address', 'wpclubmanager'); ?></label>
			<textarea name="term_meta[wpcm_address]" id="term_meta[wpcm_address]" rows="5" cols="40"><?php echo (isset($term_meta['wpcm_address'])&&!empty($term_meta['wpcm_address'])) ? $term_meta['wpcm_address'] : '' ?></textarea>
			<p><?php _e('The venue address is used to display a map of the venue.', 'wpclubmanager'); ?></p>
		</div>
		<div class="form-field">
			<label for="term_meta[wpcm_capacity]"><?php _e('Venue Capacity', 'wpclubmanager'); ?></label>
			<input name="term_meta[wpcm_capacity]" id="term_meta[wpcm_capacity]" type="text" value="<?php echo (isset($term_meta['wpcm_capacity'])&&!empty($term_meta['wpcm_capacity'])) ? $term_meta['wpcm_capacity'] : '' ?>" size="5">
			<p><?php _e('The venue capacity is not prominent by default; however, some themes may show it.', 'wpclubmanager'); ?></p>
		</div>
	<?php }

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function venue_edit_extra_fields( $tag ) {
		
		$t_id = $tag->term_id;
		$term_meta = get_option( "taxonomy_term_$t_id" ); ?>
		
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[wpcm_address]"><?php _e('Venue Address', 'wpclubmanager'); ?></label>
			</th>
			<td>
				<textarea name="term_meta[wpcm_address]" id="term_meta[wpcm_address]" rows="5" cols="40"><?php echo (isset($term_meta['wpcm_address'])&&!empty($term_meta['wpcm_address'])) ? $term_meta['wpcm_address'] : '' ?></textarea>
				<p class="description"><?php _e('The venue address is used to display a map of the venue.', 'wpclubmanager'); ?></p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[wpcm_capacity]"><?php _e('Venue Capacity', 'wpclubmanager'); ?></label>
			</th>
			<td>
				<input name="term_meta[wpcm_capacity]" id="term_meta[wpcm_capacity]" type="text" value="<?php echo (isset($term_meta['wpcm_capacity'])&&!empty($term_meta['wpcm_capacity'])) ? $term_meta['wpcm_capacity'] : '' ?>" size="5">
				<p class="description"><?php _e('The venue capacity is not prominent by default; however, some themes may show it.', 'wpclubmanager'); ?></p>
			</td>
		</tr>
	<?php }

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function save_venue_extra_fields( $term_id ) {
		
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "taxonomy_term_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ){
				if ( isset( $_POST['term_meta'][$key] ) ){
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			update_option( "taxonomy_term_$t_id", $term_meta );
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function venue_edit_columns($columns) {
		
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"name" => __('Name', 'wpclubmanager'),
			"address" => __('Address', 'wpclubmanager'),
			"capacity" => __('Capacity', 'wpclubmanager')
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function venue_custom_columns($value, $column, $t_id) {
		
		global $post;

		$term_meta = get_option( "taxonomy_term_$t_id" );

		switch ($column) {
		case 'address':
			echo (isset($term_meta['wpcm_address'])&&!empty($term_meta['wpcm_address'])) ? $term_meta['wpcm_address'] : '';
			break;
		case 'capacity':
			echo (isset($term_meta['wpcm_capacity'])&&!empty($term_meta['wpcm_capacity'])) ? $term_meta['wpcm_capacity'] : '';
			break;
		}
	}
}

new WPCM_Admin_Taxonomies();