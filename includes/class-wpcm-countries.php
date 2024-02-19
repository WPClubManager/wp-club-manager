<?php
/**
 * WPClubManager countries
 *
 * The WPClubManager countries class stores country/state data.
 *
 * @class       WPCM_Countries
 * @version     2.2.1
 * @package     WPClubManager/Classes
 * @category    Class
 * @author      ClubPress
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPCM_Countries {

	/** @var array Array of countries */
	public $countries;

	/** @var array Array of locales */
	public $locale;

	/**
	 * Constructor for the counties class - defines all countries and states.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// global $wpclubmanager;

		$this->countries = apply_filters( 'wpclubmanager_countries', array(
			'af' => __( 'Afghanistan', 'wp-club-manager' ),
			'ax' => __( '&#197;land Islands', 'wp-club-manager' ),
			'al' => __( 'Albania', 'wp-club-manager' ),
			'dz' => __( 'Algeria', 'wp-club-manager' ),
			'as' => __( 'American Samoa', 'wp-club-manager' ),
			'ad' => __( 'Andorra', 'wp-club-manager' ),
			'ao' => __( 'Angola', 'wp-club-manager' ),
			'ai' => __( 'Anguilla', 'wp-club-manager' ),
			'aq' => __( 'Antarctica', 'wp-club-manager' ),
			'ag' => __( 'Antigua and Barbuda', 'wp-club-manager' ),
			'ar' => __( 'Argentina', 'wp-club-manager' ),
			'am' => __( 'Armenia', 'wp-club-manager' ),
			'aw' => __( 'Aruba', 'wp-club-manager' ),
			'au' => __( 'Australia', 'wp-club-manager' ),
			'at' => __( 'Austria', 'wp-club-manager' ),
			'az' => __( 'Azerbaijan', 'wp-club-manager' ),
			'bs' => __( 'Bahamas', 'wp-club-manager' ),
			'bh' => __( 'Bahrain', 'wp-club-manager' ),
			'bd' => __( 'Bangladesh', 'wp-club-manager' ),
			'bb' => __( 'Barbados', 'wp-club-manager' ),
			'by' => __( 'Belarus', 'wp-club-manager' ),
			'be' => __( 'Belgium', 'wp-club-manager' ),
			'bz' => __( 'Belize', 'wp-club-manager' ),
			'bj' => __( 'Benin', 'wp-club-manager' ),
			'bm' => __( 'Bermuda', 'wp-club-manager' ),
			'bt' => __( 'Bhutan', 'wp-club-manager' ),
			'bo' => __( 'Bolivia', 'wp-club-manager' ),
			'ba' => __( 'Bosnia and Herzegovina', 'wp-club-manager' ),
			'bw' => __( 'Botswana', 'wp-club-manager' ),
			'bv' => __( 'Bouvet Island', 'wp-club-manager' ),
			'br' => __( 'Brazil', 'wp-club-manager' ),
			'io' => __( 'British Indian Ocean Territory', 'wp-club-manager' ),
			'bn' => __( 'Brunei Darussalam', 'wp-club-manager' ),
			'bg' => __( 'Bulgaria', 'wp-club-manager' ),
			'bf' => __( 'Burkina Faso', 'wp-club-manager' ),
			'bi' => __( 'Burundi', 'wp-club-manager' ),
			'kh' => __( 'Cambodia', 'wp-club-manager' ),
			'cm' => __( 'Cameroon', 'wp-club-manager' ),
			'ca' => __( 'Canada', 'wp-club-manager' ),
			'cv' => __( 'Cape Verde', 'wp-club-manager' ),
			'ct' => __( 'Catalonia', 'wp-club-manager' ),
			'ky' => __( 'Cayman Islands', 'wp-club-manager' ),
			'cf' => __( 'Central African Republic', 'wp-club-manager' ),
			'td' => __( 'Chad', 'wp-club-manager' ),
			'cl' => __( 'Chile', 'wp-club-manager' ),
			'cn' => __( 'China', 'wp-club-manager' ),
			'cx' => __( 'Christmas Island', 'wp-club-manager' ),
			'cc' => __( 'Cocos (Keeling) Islands', 'wp-club-manager' ),
			'co' => __( 'Colombia', 'wp-club-manager' ),
			'km' => __( 'Comoros', 'wp-club-manager' ),
			'cg' => __( 'Congo', 'wp-club-manager' ),
			'cd' => __( 'Congo DR', 'wp-club-manager' ),
			'ck' => __( 'Cook Islands', 'wp-club-manager' ),
			'cr' => __( 'Costa Rica', 'wp-club-manager' ),
			'hr' => __( 'Croatia', 'wp-club-manager' ),
			'cu' => __( 'Cuba', 'wp-club-manager' ),
			'cy' => __( 'Cyprus', 'wp-club-manager' ),
			'cz' => __( 'Czech Republic', 'wp-club-manager' ),
			'dk' => __( 'Denmark', 'wp-club-manager' ),
			'dj' => __( 'Djibouti', 'wp-club-manager' ),
			'dm' => __( 'Dominica', 'wp-club-manager' ),
			'do' => __( 'Dominican Republic', 'wp-club-manager' ),
			'ec' => __( 'Ecuador', 'wp-club-manager' ),
			'eg' => __( 'Egypt', 'wp-club-manager' ),
			'sv' => __( 'El Salvador', 'wp-club-manager' ),
			'en' => __( 'England', 'wp-club-manager' ),
			'gq' => __( 'Equatorial Guinea', 'wp-club-manager' ),
			'er' => __( 'Eritrea', 'wp-club-manager' ),
			'ee' => __( 'Estonia', 'wp-club-manager' ),
			'et' => __( 'Ethiopia', 'wp-club-manager' ),
			'fk' => __( 'Falkland Islands', 'wp-club-manager' ),
			'fo' => __( 'Faroe Islands', 'wp-club-manager' ),
			'fj' => __( 'Fiji', 'wp-club-manager' ),
			'fi' => __( 'Finland', 'wp-club-manager' ),
			'fr' => __( 'France', 'wp-club-manager' ),
			'gf' => __( 'French Guiana', 'wp-club-manager' ),
			'pf' => __( 'French Polynesia', 'wp-club-manager' ),
			'tf' => __( 'French Southern Territories', 'wp-club-manager' ),
			'ga' => __( 'Gabon', 'wp-club-manager' ),
			'gm' => __( 'Gambia', 'wp-club-manager' ),
			'ge' => __( 'Georgia', 'wp-club-manager' ),
			'de' => __( 'Germany', 'wp-club-manager' ),
			'gh' => __( 'Ghana', 'wp-club-manager' ),
			'gi' => __( 'Gibraltar', 'wp-club-manager' ),
			'gr' => __( 'Greece', 'wp-club-manager' ),
			'gl' => __( 'Greenland', 'wp-club-manager' ),
			'gd' => __( 'Grenada', 'wp-club-manager' ),
			'gp' => __( 'Guadeloupe', 'wp-club-manager' ),
			'gu' => __( 'Guam', 'wp-club-manager' ),
			'gt' => __( 'Guatemala', 'wp-club-manager' ),
			'gg' => __( 'Guernsey', 'wp-club-manager' ),
			'gn' => __( 'Guinea', 'wp-club-manager' ),
			'gw' => __( 'Guinea-Bissau', 'wp-club-manager' ),
			'gy' => __( 'Guyana', 'wp-club-manager' ),
			'ht' => __( 'Haiti', 'wp-club-manager' ),
			'hm' => __( 'Heard Island and McDonald Islands', 'wp-club-manager' ),
			'va' => __( 'Holy See (Vatican City State)', 'wp-club-manager' ),
			'hn' => __( 'Honduras', 'wp-club-manager' ),
			'hk' => __( 'Hong Kong', 'wp-club-manager' ),
			'hu' => __( 'Hungary', 'wp-club-manager' ),
			'is' => __( 'Iceland', 'wp-club-manager' ),
			'in' => __( 'India', 'wp-club-manager' ),
			'id' => __( 'Indonesia', 'wp-club-manager' ),
			'ir' => __( 'Iran', 'wp-club-manager' ),
			'iq' => __( 'Iraq', 'wp-club-manager' ),
			'ie' => __( 'Ireland', 'wp-club-manager' ),
			'im' => __( 'Isle of Man', 'wp-club-manager' ),
			'il' => __( 'Israel', 'wp-club-manager' ),
			'it' => __( 'Italy', 'wp-club-manager' ),
			'ci' => __( 'Ivory Coast', 'wp-club-manager' ),
			'jm' => __( 'Jamaica', 'wp-club-manager' ),
			'jp' => __( 'Japan', 'wp-club-manager' ),
			'je' => __( 'Jersey', 'wp-club-manager' ),
			'jo' => __( 'Jordan', 'wp-club-manager' ),
			'kz' => __( 'Kazakhstan', 'wp-club-manager' ),
			'ke' => __( 'Kenya', 'wp-club-manager' ),
			'ki' => __( 'Kiribati', 'wp-club-manager' ),
			'ks' => __( 'Kosovo', 'wp-club-manager' ),
			'kw' => __( 'Kuwait', 'wp-club-manager' ),
			'kg' => __( 'Kyrgyzstan', 'wp-club-manager' ),
			'la' => __( 'Laos', 'wp-club-manager' ),
			'lv' => __( 'Latvia', 'wp-club-manager' ),
			'lb' => __( 'Lebanon', 'wp-club-manager' ),
			'ls' => __( 'Lesotho', 'wp-club-manager' ),
			'lr' => __( 'Liberia', 'wp-club-manager' ),
			'ly' => __( 'Libya', 'wp-club-manager' ),
			'li' => __( 'Liechtenstein', 'wp-club-manager' ),
			'lt' => __( 'Lithuania', 'wp-club-manager' ),
			'lu' => __( 'Luxembourg', 'wp-club-manager' ),
			'mo' => __( 'Macao', 'wp-club-manager' ),
			'mk' => __( 'North Macedonia', 'wp-club-manager' ),
			'mg' => __( 'Madagascar', 'wp-club-manager' ),
			'mw' => __( 'Malawi', 'wp-club-manager' ),
			'my' => __( 'Malaysia', 'wp-club-manager' ),
			'mv' => __( 'Maldives', 'wp-club-manager' ),
			'ml' => __( 'Mali', 'wp-club-manager' ),
			'mt' => __( 'Malta', 'wp-club-manager' ),
			'mh' => __( 'Marshall Islands', 'wp-club-manager' ),
			'mq' => __( 'Martinique', 'wp-club-manager' ),
			'mr' => __( 'Mauritania', 'wp-club-manager' ),
			'mu' => __( 'Mauritius', 'wp-club-manager' ),
			'yt' => __( 'Mayotte', 'wp-club-manager' ),
			'mx' => __( 'Mexico', 'wp-club-manager' ),
			'fm' => __( 'Micronesia, Federal States of', 'wp-club-manager' ),
			'md' => __( 'Moldova, Republic of', 'wp-club-manager' ),
			'mc' => __( 'Monaco', 'wp-club-manager' ),
			'mn' => __( 'Mongolia', 'wp-club-manager' ),
			'ms' => __( 'Monserrat', 'wp-club-manager' ),
			'me' => __( 'Montenegro', 'wp-club-manager' ),
			'ma' => __( 'Morocco', 'wp-club-manager' ),
			'kp' => __( 'North Korea', 'wp-club-manager' ),
			'mz' => __( 'Mozambique', 'wp-club-manager' ),
			'mm' => __( 'Myanmar', 'wp-club-manager' ),
			'na' => __( 'Namibia', 'wp-club-manager' ),
			'nr' => __( 'Nauru', 'wp-club-manager' ),
			'np' => __( 'Nepal', 'wp-club-manager' ),
			'nl' => __( 'Netherlands', 'wp-club-manager' ),
			'nc' => __( 'New Caledonia', 'wp-club-manager' ),
			'nz' => __( 'New Zealand', 'wp-club-manager' ),
			'ni' => __( 'Nicaragua', 'wp-club-manager' ),
			'ne' => __( 'Niger', 'wp-club-manager' ),
			'ng' => __( 'Nigeria', 'wp-club-manager' ),
			'nu' => __( 'Niue', 'wp-club-manager' ),
			'nf' => __( 'Norfolk Island', 'wp-club-manager' ),
			'nd' => __( 'Northern Ireland', 'wp-club-manager' ),
			'mp' => __( 'Northern Mariana Islands', 'wp-club-manager' ),
			'no' => __( 'Norway', 'wp-club-manager' ),
			'om' => __( 'Oman', 'wp-club-manager' ),
			'pk' => __( 'Pakistan', 'wp-club-manager' ),
			'pw' => __( 'Palau', 'wp-club-manager' ),
			'ps' => __( 'Palestine, State of', 'wp-club-manager' ),
			'pa' => __( 'Panama', 'wp-club-manager' ),
			'pg' => __( 'Papua New Guinea', 'wp-club-manager' ),
			'py' => __( 'Paraguay', 'wp-club-manager' ),
			'pe' => __( 'Peru', 'wp-club-manager' ),
			'ph' => __( 'Philippines', 'wp-club-manager' ),
			'pn' => __( 'Pitcairn', 'wp-club-manager' ),
			'pl' => __( 'Poland', 'wp-club-manager' ),
			'pt' => __( 'Portugal', 'wp-club-manager' ),
			'pr' => __( 'Puerto Rico', 'wp-club-manager' ),
			'qa' => __( 'Qatar', 'wp-club-manager' ),
			're' => __( 'Reunion', 'wp-club-manager' ),
			'ro' => __( 'Romania', 'wp-club-manager' ),
			'ru' => __( 'Russian Federation', 'wp-club-manager' ),
			'rw' => __( 'Rwanda', 'wp-club-manager' ),
			'sh' => __( 'Saint Helena', 'wp-club-manager' ),
			'kn' => __( 'Saint Kitts and Nevis', 'wp-club-manager' ),
			'lc' => __( 'Saint Lucia', 'wp-club-manager' ),
			'pm' => __( 'Saint Pierre & Miquelon', 'wp-club-manager' ),
			'vc' => __( 'Saint Vincent and the Grenadines', 'wp-club-manager' ),
			'ws' => __( 'Samoa', 'wp-club-manager' ),
			'sm' => __( 'San Marino', 'wp-club-manager' ),
			'st' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'wp-club-manager' ),
			'sa' => __( 'Saudi Arabia', 'wp-club-manager' ),
			'sf' => __( 'Scotland', 'wp-club-manager' ),
			'sn' => __( 'Senegal', 'wp-club-manager' ),
			'rs' => __( 'Serbia', 'wp-club-manager' ),
			'sc' => __( 'Seychelles', 'wp-club-manager' ),
			'sl' => __( 'Sierre Leone', 'wp-club-manager' ),
			'sg' => __( 'Singapore', 'wp-club-manager' ),
			'sk' => __( 'Slovakia', 'wp-club-manager' ),
			'si' => __( 'Slovenia', 'wp-club-manager' ),
			'sb' => __( 'Solomon Islands', 'wp-club-manager' ),
			'so' => __( 'Somalia', 'wp-club-manager' ),
			'za' => __( 'South Africa', 'wp-club-manager' ),
			'gs' => __( 'South Georgia / Sandwich Islands', 'wp-club-manager' ),
			'kr' => __( 'South Korea', 'wp-club-manager' ),
			'es' => __( 'Spain', 'wp-club-manager' ),
			'lk' => __( 'Sri Lanka', 'wp-club-manager' ),
			'sj' => __( 'Svalbard and Jan Mayen', 'wp-club-manager' ),
			'sd' => __( 'Sudan', 'wp-club-manager' ),
			'sr' => __( 'Suriname', 'wp-club-manager' ),
			'sz' => __( 'Swaziland', 'wp-club-manager' ),
			'se' => __( 'Sweden', 'wp-club-manager' ),
			'ch' => __( 'Switzerland', 'wp-club-manager' ),
			'sy' => __( 'Syrian Arab Republic', 'wp-club-manager' ),
			'tw' => __( 'Taiwan, Province of China', 'wp-club-manager' ),
			'tj' => __( 'Tajikstan', 'wp-club-manager' ),
			'tz' => __( 'Tanzania, United Republic of', 'wp-club-manager' ),
			'th' => __( 'Thailand', 'wp-club-manager' ),
			'tl' => __( 'Timor-Leste', 'wp-club-manager' ),
			'tg' => __( 'Togo', 'wp-club-manager' ),
			'tk' => __( 'Tokelau', 'wp-club-manager' ),
			'to' => __( 'Tonga', 'wp-club-manager' ),
			'tt' => __( 'Trinidad and Tobago', 'wp-club-manager' ),
			'tn' => __( 'Tunisia', 'wp-club-manager' ),
			'tr' => __( 'Turkey', 'wp-club-manager' ),
			'tm' => __( 'Turkmenistan', 'wp-club-manager' ),
			'tc' => __( 'Turks and Caicos Islands', 'wp-club-manager' ),
			'tv' => __( 'Tuvalu', 'wp-club-manager' ),
			'ug' => __( 'Uganda', 'wp-club-manager' ),
			'ua' => __( 'Ukraine', 'wp-club-manager' ),
			'ae' => __( 'United Arab Emirates', 'wp-club-manager' ),
			'gb' => __( 'United Kingdom (UK)', 'wp-club-manager' ),
			'us' => __( 'United States (US)', 'wp-club-manager' ),
			'uy' => __( 'Uruguay', 'wp-club-manager' ),
			'um' => __( 'US Minor Outlying Islands', 'wp-club-manager' ),
			'uz' => __( 'Uzbekistan', 'wp-club-manager' ),
			'vu' => __( 'Vanuatu', 'wp-club-manager' ),
			've' => __( 'Venezuela', 'wp-club-manager' ),
			'vn' => __( 'Vietnam', 'wp-club-manager' ),
			'vg' => __( 'Virgin Islands, British', 'wp-club-manager' ),
			'vi' => __( 'Virgin Islands, U.S.', 'wp-club-manager' ),
			'wl' => __( 'Wales', 'wp-club-manager' ),
			'wf' => __( 'Wallis and Futuna', 'wp-club-manager' ),
			'eh' => __( 'Western Sahara', 'wp-club-manager' ),
			'ye' => __( 'Yemen', 'wp-club-manager' ),
			'an' => __( 'Yugoslavia', 'wp-club-manager' ),
			'zm' => __( 'Zambia', 'wp-club-manager' ),
			'zw' => __( 'Zimbabwe', 'wp-club-manager' ),
		));
	}

	/**
	 * Outputs the list of countries and states for use in dropdown boxes.
	 *
	 * @access public
	 * @param string $selected_country (default: '')
	 * @param bool   $escape (default: false)
	 * @return void
	 */
	public function country_dropdown_options( $country = '', $escape = false ) {

		if ( apply_filters( 'wpclubmanager_sort_countries', true ) ) {
			asort( $this->countries );
		}

		if ( $this->countries ) {
			foreach ( $this->countries as $key => $value ) :
				echo '<option';
				if ( $country == $key ) {
					echo ' selected="selected"';
				}
				echo ' value="' . esc_attr( $key ) . '">' . ( $escape ? esc_js( $value ) : $value ) . '</option>';
			endforeach;
		}
	}
}
