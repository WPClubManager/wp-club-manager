<?php
/**
 * Handles taxonomies in admin
 *
 * @class       WPCM_Admin_Taxonomies
 * @version     2.2.0
 * @package     WPClubManager/Admin
 * @category    Class
 * @author      ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Admin_Taxonomies
 */
class WPCM_Admin_Taxonomies {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'create_wpcm_season', array( $this, 'save_season_tax_positions' ), 10, 2 );
		add_action( 'create_wpcm_position', array( $this, 'save_position_tax_positions' ), 10, 2 );
		add_action( 'create_wpcm_jobs', array( $this, 'save_jobs_tax_positions' ), 10, 2 );

		add_action( 'wpcm_team_add_form_fields', array( $this, 'team_add_new_extra_fields' ), 10, 2 );
		add_action( 'wpcm_team_edit_form_fields', array( $this, 'team_edit_extra_fields' ), 10, 2 );

		add_action( 'edited_wpcm_team', array( $this, 'save_team_extra_fields' ), 10, 2 );
		add_action( 'create_wpcm_team', array( $this, 'save_team_extra_fields' ), 10, 2 );

		add_action( 'wpcm_comp_add_form_fields', array( $this, 'comp_add_new_extra_fields' ), 10, 2 );
		add_action( 'wpcm_comp_edit_form_fields', array( $this, 'comp_edit_extra_fields' ), 10, 2 );

		add_action( 'edited_wpcm_comp', array( $this, 'save_comp_extra_fields' ), 10, 2 );
		add_action( 'create_wpcm_comp', array( $this, 'save_comp_extra_fields' ), 10, 2 );

		add_action( 'wpcm_venue_add_form_fields', array( $this, 'venue_add_new_extra_fields' ), 10, 2 );
		add_action( 'wpcm_venue_edit_form_fields', array( $this, 'venue_edit_extra_fields' ), 10, 2 );

		add_action( 'edited_wpcm_venue', array( $this, 'save_venue_extra_fields' ), 10, 2 );
		add_action( 'create_wpcm_venue', array( $this, 'save_venue_extra_fields' ), 10, 2 );

		add_action( 'manage_wpcm_comp_custom_column', array( $this, 'comp_custom_columns' ), 5, 3 );
		add_action( 'manage_wpcm_season_custom_column', array( $this, 'season_custom_columns' ), 5, 3 );
		add_action( 'manage_wpcm_team_custom_column', array( $this, 'team_custom_columns' ), 5, 3 );
		add_action( 'manage_wpcm_venue_custom_column', array( $this, 'venue_custom_columns' ), 5, 3 );
		add_action( 'manage_wpcm_position_custom_column', array( $this, 'position_custom_columns' ), 5, 3 );
		add_action( 'manage_wpcm_jobs_custom_column', array( $this, 'position_custom_columns' ), 5, 3 );

		add_filter( 'manage_edit-wpcm_comp_columns', array( $this, 'comp_edit_columns' ) );
		add_filter( 'manage_edit-wpcm_season_columns', array( $this, 'season_edit_columns' ) );
		add_filter( 'manage_edit-wpcm_team_columns', array( $this, 'team_edit_columns' ) );
		add_filter( 'manage_edit-wpcm_venue_columns', array( $this, 'venue_edit_columns' ) );
		add_filter( 'manage_edit-wpcm_position_columns', array( $this, 'position_edit_columns' ) );
		add_filter( 'manage_edit-wpcm_jobs_columns', array( $this, 'position_edit_columns' ) );
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_season_tax_positions( $term_id ) {

		$terms = get_terms( array(
			'taxonomy'   => 'wpcm_season',
			'hide_empty' => false,
			'exclude'    => $term_id,
		) );

		foreach ( $terms as $term ) {
			$pos = get_term_meta( $term->term_id, 'tax_position', true );
			update_term_meta( $term->term_id, 'tax_position', $pos + 1 );
		}
		update_term_meta( $term_id, 'tax_position', 1 );
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_position_tax_positions( $term_id ) {

		$terms = get_terms( array(
			'taxonomy'   => 'wpcm_position',
			'hide_empty' => false,
			'exclude'    => $term_id,
		) );

		foreach ( $terms as $term ) {
			$pos = get_term_meta( $term->term_id, 'tax_position', true );
			update_term_meta( $term->term_id, 'tax_position', $pos + 1 );
		}
		update_term_meta( $term_id, 'tax_position', 1 );
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_jobs_tax_positions( $term_id ) {

		$terms = get_terms( array(
			'taxonomy'   => 'wpcm_jobs',
			'hide_empty' => false,
			'exclude'    => $term_id,
		) );

		foreach ( $terms as $term ) {
			$pos = get_term_meta( $term->term_id, 'tax_position', true );
			update_term_meta( $term->term_id, 'tax_position', $pos + 1 );
		}
		update_term_meta( $term_id, 'tax_position', 1 );
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed $tag
	 *
	 * @return void
	 */
	public function team_add_new_extra_fields( $tag ) {
		?>

		<div class="form-field">
			<label for="term_meta[wpcm_team_label]"><?php esc_html_e( 'Display Name', 'wp-club-manager' ); ?></label>
			<input name="term_meta[wpcm_team_label]" id="term_meta[wpcm_team_label]" type="text" value="<?php echo ( isset( $term_meta['wpcm_team_label'] ) && ! empty( $term_meta['wpcm_team_label'] ) ) ? esc_html( $term_meta['wpcm_team_label'] ) : ''; ?>"/>
			<p><?php esc_html_e( 'The team label is used to display a shortened version of the team name.', 'wp-club-manager' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param WP_Term $tag
	 *
	 * @return void
	 */
	public function team_edit_extra_fields( $tag ) {

		$t_id      = $tag->term_id;
		$term_meta = get_option( "taxonomy_term_$t_id" );
		?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[wpcm_team_label]"><?php esc_html_e( 'Display Name', 'wp-club-manager' ); ?></label>
			</th>
			<td>
				<input name="term_meta[wpcm_team_label]" id="term_meta[wpcm_team_label]" type="text" value="<?php echo $term_meta['wpcm_team_label'] ? esc_html( $term_meta['wpcm_team_label'] ) : ''; ?>"/>
				<p class="description"><?php esc_html_e( 'The team label is used to display a shortened version of the team name.', 'wp-club-manager' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_team_extra_fields( $term_id ) {
		$term_meta_value = filter_input( INPUT_POST, 'term_meta', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( $term_meta_value ) {
			$t_id      = $term_id;
			$term_meta = get_option( "taxonomy_term_$t_id" );
			$cat_keys  = array_keys( $term_meta_value );
			foreach ( $cat_keys as $key ) {
				if ( isset( $term_meta_value[ $key ] ) ) {
					$term_meta[ $key ] = $term_meta_value[ $key ];
				}
			}
			update_option( "taxonomy_term_$t_id", $term_meta );
		}
		$terms = get_terms( array(
			'taxonomy'   => 'wpcm_team',
			'hide_empty' => false,
			'exclude'    => $term_id,
		) );

		foreach ( $terms as $term ) {
			$pos = get_term_meta( $term->term_id, 'tax_position', true );
			update_term_meta( $term->term_id, 'tax_position', $pos + 1 );
		}
		update_term_meta( $term_id, 'tax_position', 1 );
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed $columns
	 *
	 * @return array
	 */
	public function team_edit_columns( $columns ) {

		$columns = array(
			'cb'    => '<input type="checkbox" />',
			'move'  => '',
			'name'  => __( 'Name', 'wp-club-manager' ),
			'label' => __( 'Label', 'wp-club-manager' ),
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed  $value
	 * @param string $column
	 * @param int    $t_id
	 *
	 * @return void
	 */
	public function team_custom_columns( $value, $column, $t_id ) {

		global $post;

		$term_meta = get_option( "taxonomy_term_$t_id" );

		switch ( $column ) {
			case 'move':
				echo '<i class="dashicons dashicons-move"></i>';
				break;
			case 'label':
				echo esc_html( $term_meta['wpcm_team_label'] );
				break;
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed $tag
	 *
	 * @return void
	 */
	public function comp_add_new_extra_fields( $tag ) {
		?>

		<div class="form-field">
			<label for="term_meta[wpcm_comp_label]"><?php esc_html_e( 'Competition Label', 'wp-club-manager' ); ?></label>
			<input name="term_meta[wpcm_comp_label]" id="term_meta[wpcm_comp_label]" type="text" value="<?php echo ( isset( $term_meta['wpcm_comp_label'] ) && ! empty( $term_meta['wpcm_comp_label'] ) ) ? esc_html( $term_meta['wpcm_comp_label'] ) : ''; ?>"/>
			<p><?php esc_html_e( 'The competition label is used to display a shortened version of the competition name.', 'wp-club-manager' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param WP_Term $tag
	 *
	 * @return void
	 */
	public function comp_edit_extra_fields( $tag ) {

		$t_id      = $tag->term_id;
		$term_meta = get_option( "taxonomy_term_$t_id" );
		?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[wpcm_comp_label]"><?php esc_html_e( 'Competition Label', 'wp-club-manager' ); ?></label>
			</th>
			<td>
				<input name="term_meta[wpcm_comp_label]" id="term_meta[wpcm_comp_label]" type="text" value="<?php echo $term_meta['wpcm_comp_label'] ? esc_html( $term_meta['wpcm_comp_label'] ) : ''; ?>"/>
				<p class="description"><?php esc_html_e( 'The competition label is used to display a shortened version of the competition name.', 'wp-club-manager' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_comp_extra_fields( $term_id ) {
		$term_meta_value = filter_input( INPUT_POST, 'term_meta', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( $term_meta_value ) {
			$t_id      = $term_id;
			$term_meta = get_option( "taxonomy_term_$t_id" );
			$cat_keys  = array_keys( $term_meta_value );
			foreach ( $cat_keys as $key ) {
				if ( isset( $term_meta_value[ $key ] ) ) {
					$term_meta[ $key ] = $term_meta_value[ $key ];
				}
			}
			update_option( "taxonomy_term_$t_id", $term_meta );
		}
		$terms = get_terms( array(
			'taxonomy'   => 'wpcm_season',
			'hide_empty' => false,
			'exclude'    => $term_id,
		) );

		foreach ( $terms as $term ) {
			$pos = get_term_meta( $term->term_id, 'tax_position', true );
			update_term_meta( $term->term_id, 'tax_position', $pos + 1 );
		}
		update_term_meta( $term_id, 'tax_position', 1 );
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function comp_edit_columns( $columns ) {

		$columns = array(
			'cb'    => '<input type="checkbox" />',
			'move'  => '',
			'name'  => __( 'Name', 'wp-club-manager' ),
			'label' => __( 'Label', 'wp-club-manager' ),
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed  $value
	 * @param string $column
	 * @param int    $t_id
	 *
	 * @return void
	 */
	public function comp_custom_columns( $value, $column, $t_id ) {

		global $post;

		$term_meta = get_option( "taxonomy_term_$t_id" );

		switch ( $column ) {
			case 'move':
				echo '<i class="dashicons dashicons-move"></i>';
				break;
			case 'label':
				echo esc_html( $term_meta['wpcm_comp_label'] );
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
	public function season_edit_columns( $columns ) {

		$columns = array(
			'cb'   => '<input type="checkbox" />',
			'move' => '',
			'name' => __( 'Name', 'wp-club-manager' ),
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed  $value
	 * @param string $column
	 * @param int    $t_id
	 *
	 * @return void
	 */
	public function season_custom_columns( $value, $column, $t_id ) {

		global $post;

		switch ( $column ) {
			case 'move':
				echo '<i class="dashicons dashicons-move"></i>';
				break;
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed $tag
	 *
	 * @return void
	 */
	public function venue_add_new_extra_fields( $tag ) {

		$args = array(
			'taxonomy' => 'wpcm_venue',
			'orderby'    => 'id',
			'order'      => 'DESC',
			'hide_empty' => false,
		);
		// Get latitude and longitude from the last added venue
		$terms = get_terms( $args );
		if ( $terms ) {
			$term      = reset( $terms );
			$t_id      = $term->term_id;
			$term_meta = get_option( "taxonomy_term_$t_id" );
			$address   = $term_meta['wpcm_address'];
			$latitude  = $term_meta['wpcm_latitude'];
			$longitude = $term_meta['wpcm_longitude'];
		} else {
			$address   = 'London Stadium, London, E20 2ST UK';
			$latitude  = '51.5391098892326';
			$longitude = '-0.016526945751934363';
		}
		$map_service = get_option( 'wpcm_map_select', 'google' );
		?>

		<div class="form-field">
			<label for="term_meta[wpcm_address]"><?php esc_html_e( 'Venue Address', 'wp-club-manager' ); ?></label>
			<input type="text" class="wpcm-address" name="term_meta[wpcm_address]" id="term_meta[wpcm_address]" value="<?php echo esc_attr( $address ); ?>">
			<?php
			if ( 'google' == $map_service ) {
				?>
				<p><div class="wpcm-location-picker"></div></p>
				<p class="description">
					<?php esc_html_e( "Drag the marker to the venue's location.", 'wp-club-manager' ); ?>
				</p>
				<?php
			}
			?>
		</div>

		<input type="hidden" class="wpcm-latitude" name="term_meta[wpcm_latitude]" id="term_meta[wpcm_latitude]" value="<?php echo esc_attr( $latitude ); ?>">

		<input type="hidden" class="wpcm-longitude" name="term_meta[wpcm_longitude]" id="term_meta[wpcm_longitude]" value="<?php echo esc_attr( $longitude ); ?>">

		<div class="form-field">
			<label for="term_meta[wpcm_capacity]"><?php esc_html_e( 'Venue Capacity', 'wp-club-manager' ); ?></label>
			<input name="term_meta[wpcm_capacity]" id="term_meta[wpcm_capacity]" type="text" value="<?php echo ( isset( $term_meta['wpcm_capacity'] ) && ! empty( $term_meta['wpcm_capacity'] ) ) ? esc_html( $term_meta['wpcm_capacity'] ) : ''; ?>" size="8">
		</div>
		<?php
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed $tag
	 *
	 * @return void
	 */
	public function venue_edit_extra_fields( $tag ) {

		$t_id      = $tag->term_id;
		$term_meta = get_option( "taxonomy_term_$t_id" );
		$address   = $term_meta['wpcm_address'];
		if ( $address ) {
			$coordinates = new WPCM_Geocoder( $address );
			$latitude    = $coordinates->lat;
			$longitude   = $coordinates->lng;
		}

		// $t_id = $tag->term_id;
		// $term_meta = get_option( "taxonomy_term_$t_id" );
		// $address = $term_meta['wpcm_address'];
		// if( $address ) {
		// $coordinates = wpcm_decode_address( $address );
		// if ( is_array ( $coordinates ) ) {
		// $latitude = $coordinates['lat'];
		// $longitude = $coordinates['lng'];
		// }
		// }
		$map_service = get_option( 'wpcm_map_select', 'google' );
		?>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[wpcm_address]"><?php esc_html_e( 'Address', 'wp-club-manager' ); ?></label></th>
			<td>
				<input type="text" class="wpcm-address" name="term_meta[wpcm_address]" id="term_meta[wpcm_address]" value="<?php echo ( isset( $term_meta['wpcm_address'] ) && ! empty( $term_meta['wpcm_address'] ) ) ? esc_html( $term_meta['wpcm_address'] ) : ''; ?>">
				<?php
				if ( 'google' == $map_service ) {
					?>
					<p><div class="wpcm-location-picker"></div></p>
					<p class="description">
						<?php esc_html_e( "Drag the marker to the venue's location.", 'wp-club-manager' ); ?>
					</p>
					<?php
				}
				?>
			</td>
		</tr>

		<input type="hidden" <?php echo 'google' == $map_service ? 'class="wpcm-latitude"' : ''; ?> name="term_meta[wpcm_latitude]" id="term_meta[wpcm_latitude]" value="<?php echo ( isset( $term_meta['wpcm_latitude'] ) && ! empty( $term_meta['wpcm_latitude'] ) ) ? esc_html( $term_meta['wpcm_latitude'] ) : esc_html( $latitude ); ?>">

		<input type="hidden" <?php echo 'google' == $map_service ? 'class="wpcm-longitude"' : ''; ?> name="term_meta[wpcm_longitude]" id="term_meta[wpcm_longitude]" value="<?php echo ( isset( $term_meta['wpcm_longitude'] ) && ! empty( $term_meta['wpcm_longitude'] ) ) ? esc_html( $term_meta['wpcm_longitude'] ) : esc_html( $longitude ); ?>">

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[wpcm_capacity]"><?php esc_html_e( 'Venue Capacity', 'wp-club-manager' ); ?></label>
			</th>
			<td>
				<input name="term_meta[wpcm_capacity]" id="term_meta[wpcm_capacity]" type="text" value="<?php echo ( isset( $term_meta['wpcm_capacity'] ) && ! empty( $term_meta['wpcm_capacity'] ) ) ? esc_html( $term_meta['wpcm_capacity'] ) : ''; ?>" size="8">
			</td>
		</tr>
		<?php
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_venue_extra_fields( $term_id ) {

		$map_service = get_option( 'wpcm_map_select', 'google' );

		$term_meta_value = filter_input( INPUT_POST, 'term_meta', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( 'osm' == $map_service ) {

			if ( $term_meta_value ) {
				$t_id                        = $term_id;
				$term_meta                   = get_option( "taxonomy_term_$t_id" );
				$address                     = $term_meta_value['wpcm_address'];
				$location                    = new WPCM_Geocoder( $address );
				$lat                         = $location->lat;
				$lng                         = $location->lng;
				$term_meta['wpcm_address']   = $term_meta_value['wpcm_address'];
				$term_meta['wpcm_latitude']  = $lat;
				$term_meta['wpcm_longitude'] = $lng;
				$term_meta['wpcm_capacity']  = $term_meta_value['wpcm_capacity'];
				update_option( 'taxonomy_term_' . $t_id, $term_meta );
			}
		} elseif ( $term_meta_value ) {

				$t_id      = $term_id;
				$term_meta = get_option( "taxonomy_term_$t_id" );
				$cat_keys  = array_keys( $term_meta_value );
			foreach ( $cat_keys as $key ) {
				if ( isset( $term_meta_value[ $key ] ) ) {
					$term_meta[ $key ] = $term_meta_value[ $key ];
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
	public function venue_edit_columns( $columns ) {

		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'name'     => __( 'Name', 'wp-club-manager' ),
			'address'  => __( 'Address', 'wp-club-manager' ),
			'capacity' => __( 'Capacity', 'wp-club-manager' ),
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed  $value
	 * @param string $column
	 * @param int    $t_id
	 *
	 * @return void
	 */
	public function venue_custom_columns( $value, $column, $t_id ) {

		global $post;

		$term_meta = get_option( "taxonomy_term_$t_id" );

		switch ( $column ) {
			case 'address':
				echo ( isset( $term_meta['wpcm_address'] ) && ! empty( $term_meta['wpcm_address'] ) ) ? esc_html( $term_meta['wpcm_address'] ) : '';
				break;
			case 'capacity':
				echo ( isset( $term_meta['wpcm_capacity'] ) && ! empty( $term_meta['wpcm_capacity'] ) ) ? esc_html( $term_meta['wpcm_capacity'] ) : '';
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
	public function position_edit_columns( $columns ) {

		$columns = array(
			'cb'    => '<input type="checkbox" />',
			'move'  => '',
			'name'  => __( 'Name', 'wp-club-manager' ),
			'posts' => __( 'Count', 'wp-club-manager' ),
		);

		return $columns;
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed  $value
	 * @param string $column
	 * @param string $t_id
	 *
	 * @return void
	 */
	public function position_custom_columns( $value, $column, $t_id ) {

		global $post;

		$term_meta = get_option( "taxonomy_term_$t_id" );

		switch ( $column ) {
			case 'move':
				echo '<i class="dashicons dashicons-move"></i>';
				break;
		}
	}
}

new WPCM_Admin_Taxonomies();
