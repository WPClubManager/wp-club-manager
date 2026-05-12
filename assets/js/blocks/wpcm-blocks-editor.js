/**
 * WP Club Manager - Gutenberg Blocks
 *
 * Registers editor UI for all WPCM blocks using ServerSideRender.
 * Each block delegates rendering to the corresponding shortcode class
 * on the server, providing a live preview in the editor.
 *
 * @package WPClubManager
 */

( function() {
	var el = wp.element.createElement;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;
	var __ = wp.i18n.__;

	var orderOptions = [
		{ value: '', label: __( 'Default', 'wp-club-manager' ) },
		{ value: 'ASC', label: __( 'Ascending', 'wp-club-manager' ) },
		{ value: 'DESC', label: __( 'Descending', 'wp-club-manager' ) },
	];

	var nameFormatOptions = [
		{ value: 'full', label: __( 'Full Name', 'wp-club-manager' ) },
		{ value: 'last', label: __( 'Last Name', 'wp-club-manager' ) },
		{ value: 'first', label: __( 'First Name', 'wp-club-manager' ) },
	];

	var formatOptions = [
		{ value: '', label: __( 'All', 'wp-club-manager' ) },
		{ value: 'fixtures', label: __( 'Fixtures', 'wp-club-manager' ) },
		{ value: 'results', label: __( 'Results', 'wp-club-manager' ) },
	];

	var venueOptions = [
		{ value: '', label: __( 'All', 'wp-club-manager' ) },
		{ value: 'home', label: __( 'Home', 'wp-club-manager' ) },
		{ value: 'away', label: __( 'Away', 'wp-club-manager' ) },
	];

	var dateRangeOptions = [
		{ value: '', label: __( 'All', 'wp-club-manager' ) },
		{ value: 'last_week', label: __( 'Last Week', 'wp-club-manager' ) },
		{ value: 'next_week', label: __( 'Next Week', 'wp-club-manager' ) },
	];

	/**
	 * Create a setter function for a specific attribute.
	 */
	function makeSetter( setAttributes, attr ) {
		return function( value ) {
			var update = {};
			update[ attr ] = value;
			setAttributes( update );
		};
	}

	/**
	 * Create a toggle setter that converts boolean to '1'/'0'.
	 */
	function makeToggleSetter( setAttributes, attr ) {
		return function( value ) {
			var update = {};
			update[ attr ] = value ? '1' : '0';
			setAttributes( update );
		};
	}

	/**
	 * Build an edit component from a block name and control definitions.
	 */
	function makeEdit( blockName, controls ) {
		return function( props ) {
			var attrs = props.attributes;
			var setAttrs = props.setAttributes;

			var controlElements = [];
			for ( var i = 0; i < controls.length; i++ ) {
				var c = controls[ i ];
				if ( c.type === 'toggle' ) {
					controlElements.push(
						el( ToggleControl, {
							key: c.attr,
							label: c.label,
							checked: attrs[ c.attr ] === '1',
							onChange: makeToggleSetter( setAttrs, c.attr ),
						} )
					);
				} else if ( c.type === 'select' ) {
					controlElements.push(
						el( SelectControl, {
							key: c.attr,
							label: c.label,
							value: attrs[ c.attr ],
							options: c.options,
							onChange: makeSetter( setAttrs, c.attr ),
						} )
					);
				} else {
					controlElements.push(
						el( TextControl, {
							key: c.attr,
							label: c.label,
							value: attrs[ c.attr ] || '',
							onChange: makeSetter( setAttrs, c.attr ),
						} )
					);
				}
			}

			return el( 'div', { className: props.className },
				el( InspectorControls, {},
					el( PanelBody, { title: __( 'Settings', 'wp-club-manager' ) },
						controlElements
					)
				),
				el( ServerSideRender, {
					block: blockName,
					attributes: attrs,
				} )
			);
		};
	}

	// -----------------------------------------------------------------------
	// Match List
	// -----------------------------------------------------------------------
	registerBlockType( 'wpcm/match-list', {
		edit: makeEdit( 'wpcm/match-list', [
			{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
			{ attr: 'format', label: __( 'Format', 'wp-club-manager' ), type: 'select', options: formatOptions },
			{ attr: 'limit', label: __( 'Number of matches', 'wp-club-manager' ) },
			{ attr: 'comp', label: __( 'Competition ID', 'wp-club-manager' ) },
			{ attr: 'season', label: __( 'Season ID', 'wp-club-manager' ) },
			{ attr: 'team', label: __( 'Team ID', 'wp-club-manager' ) },
			{ attr: 'venue', label: __( 'Venue', 'wp-club-manager' ), type: 'select', options: venueOptions },
			{ attr: 'date_range', label: __( 'Date Range', 'wp-club-manager' ), type: 'select', options: dateRangeOptions },
			{ attr: 'order', label: __( 'Order', 'wp-club-manager' ), type: 'select', options: orderOptions },
			{ attr: 'show_abbr', label: __( 'Show abbreviations', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'show_thumb', label: __( 'Show club badges', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'show_comp', label: __( 'Show competition', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'show_team', label: __( 'Show team', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'show_venue', label: __( 'Show venue', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'linktext', label: __( 'Link text', 'wp-club-manager' ) },
			{ attr: 'linkpage', label: __( 'Link page ID', 'wp-club-manager' ) },
		] ),
		save: function() { return null; },
	} );

	// -----------------------------------------------------------------------
	// Player List
	// -----------------------------------------------------------------------
	registerBlockType( 'wpcm/player-list', {
		edit: makeEdit( 'wpcm/player-list', [
			{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
			{ attr: 'id', label: __( 'Roster ID', 'wp-club-manager' ) },
			{ attr: 'limit', label: __( 'Number of players', 'wp-club-manager' ) },
			{ attr: 'position', label: __( 'Position ID', 'wp-club-manager' ) },
			{ attr: 'orderby', label: __( 'Order by', 'wp-club-manager' ) },
			{ attr: 'order', label: __( 'Order', 'wp-club-manager' ), type: 'select', options: orderOptions },
			{ attr: 'columns', label: __( 'Columns', 'wp-club-manager' ) },
			{ attr: 'name_format', label: __( 'Name format', 'wp-club-manager' ), type: 'select', options: nameFormatOptions },
			{ attr: 'linktext', label: __( 'Link text', 'wp-club-manager' ) },
			{ attr: 'linkpage', label: __( 'Link page ID', 'wp-club-manager' ) },
		] ),
		save: function() { return null; },
	} );

	// -----------------------------------------------------------------------
	// Player Gallery
	// -----------------------------------------------------------------------
	registerBlockType( 'wpcm/player-gallery', {
		edit: makeEdit( 'wpcm/player-gallery', [
			{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
			{ attr: 'id', label: __( 'Roster ID', 'wp-club-manager' ) },
			{ attr: 'limit', label: __( 'Number of players', 'wp-club-manager' ) },
			{ attr: 'position', label: __( 'Position ID', 'wp-club-manager' ) },
			{ attr: 'orderby', label: __( 'Order by', 'wp-club-manager' ) },
			{ attr: 'order', label: __( 'Order', 'wp-club-manager' ), type: 'select', options: orderOptions },
			{ attr: 'columns', label: __( 'Columns', 'wp-club-manager' ) },
			{ attr: 'name_format', label: __( 'Name format', 'wp-club-manager' ), type: 'select', options: nameFormatOptions },
			{ attr: 'linktext', label: __( 'Link text', 'wp-club-manager' ) },
			{ attr: 'linkpage', label: __( 'Link page ID', 'wp-club-manager' ) },
		] ),
		save: function() { return null; },
	} );

	// -----------------------------------------------------------------------
	// Staff List
	// -----------------------------------------------------------------------
	registerBlockType( 'wpcm/staff-list', {
		edit: makeEdit( 'wpcm/staff-list', [
			{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
			{ attr: 'id', label: __( 'ID', 'wp-club-manager' ) },
			{ attr: 'limit', label: __( 'Number of staff', 'wp-club-manager' ) },
			{ attr: 'job', label: __( 'Job ID', 'wp-club-manager' ) },
			{ attr: 'orderby', label: __( 'Order by', 'wp-club-manager' ) },
			{ attr: 'order', label: __( 'Order', 'wp-club-manager' ), type: 'select', options: orderOptions },
			{ attr: 'columns', label: __( 'Columns', 'wp-club-manager' ) },
			{ attr: 'name_format', label: __( 'Name format', 'wp-club-manager' ), type: 'select', options: nameFormatOptions },
			{ attr: 'linktext', label: __( 'Link text', 'wp-club-manager' ) },
			{ attr: 'linkpage', label: __( 'Link page ID', 'wp-club-manager' ) },
		] ),
		save: function() { return null; },
	} );

	// -----------------------------------------------------------------------
	// Staff Gallery
	// -----------------------------------------------------------------------
	registerBlockType( 'wpcm/staff-gallery', {
		edit: makeEdit( 'wpcm/staff-gallery', [
			{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
			{ attr: 'id', label: __( 'ID', 'wp-club-manager' ) },
			{ attr: 'limit', label: __( 'Number of staff', 'wp-club-manager' ) },
			{ attr: 'jobs', label: __( 'Job IDs', 'wp-club-manager' ) },
			{ attr: 'orderby', label: __( 'Order by', 'wp-club-manager' ) },
			{ attr: 'order', label: __( 'Order', 'wp-club-manager' ), type: 'select', options: orderOptions },
			{ attr: 'columns', label: __( 'Columns', 'wp-club-manager' ) },
			{ attr: 'name_format', label: __( 'Name format', 'wp-club-manager' ), type: 'select', options: nameFormatOptions },
			{ attr: 'linktext', label: __( 'Link text', 'wp-club-manager' ) },
			{ attr: 'linkpage', label: __( 'Link page ID', 'wp-club-manager' ) },
		] ),
		save: function() { return null; },
	} );

	// -----------------------------------------------------------------------
	// League Table
	// -----------------------------------------------------------------------
	registerBlockType( 'wpcm/league-table', {
		edit: makeEdit( 'wpcm/league-table', [
			{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
			{ attr: 'id', label: __( 'Table ID', 'wp-club-manager' ) },
			{ attr: 'limit', label: __( 'Number of rows', 'wp-club-manager' ) },
			{ attr: 'focus', label: __( 'Focus club ID', 'wp-club-manager' ) },
			{ attr: 'columns', label: __( 'Columns', 'wp-club-manager' ) },
			{ attr: 'abbr', label: __( 'Show abbreviations', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'thumb', label: __( 'Show club badges', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'link_club', label: __( 'Link to club', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'notes', label: __( 'Show notes', 'wp-club-manager' ), type: 'toggle' },
			{ attr: 'linktext', label: __( 'Link text', 'wp-club-manager' ) },
			{ attr: 'linkpage', label: __( 'Link page ID', 'wp-club-manager' ) },
		] ),
		save: function() { return null; },
	} );

	// -----------------------------------------------------------------------
	// Map Venue
	// -----------------------------------------------------------------------
	registerBlockType( 'wpcm/map-venue', {
		edit: makeEdit( 'wpcm/map-venue', [
			{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
			{ attr: 'id', label: __( 'Venue ID', 'wp-club-manager' ) },
			{ attr: 'width', label: __( 'Width', 'wp-club-manager' ) },
			{ attr: 'height', label: __( 'Height', 'wp-club-manager' ) },
		] ),
		save: function() { return null; },
	} );

	// -----------------------------------------------------------------------
	// Match Opponents (club mode only — registered server-side conditionally)
	// -----------------------------------------------------------------------
	if ( window.wpcmBlocksConfig && window.wpcmBlocksConfig.clubMode ) {
		registerBlockType( 'wpcm/match-opponents', {
			edit: makeEdit( 'wpcm/match-opponents', [
				{ attr: 'title', label: __( 'Title', 'wp-club-manager' ) },
				{ attr: 'format', label: __( 'Format', 'wp-club-manager' ), type: 'select', options: formatOptions },
				{ attr: 'id', label: __( 'Club ID', 'wp-club-manager' ) },
				{ attr: 'limit', label: __( 'Number of matches', 'wp-club-manager' ) },
				{ attr: 'comp', label: __( 'Competition ID', 'wp-club-manager' ) },
				{ attr: 'season', label: __( 'Season ID', 'wp-club-manager' ) },
				{ attr: 'team', label: __( 'Team ID', 'wp-club-manager' ) },
				{ attr: 'venue', label: __( 'Venue', 'wp-club-manager' ), type: 'select', options: venueOptions },
				{ attr: 'date_range', label: __( 'Date Range', 'wp-club-manager' ), type: 'select', options: dateRangeOptions },
				{ attr: 'order', label: __( 'Order', 'wp-club-manager' ), type: 'select', options: orderOptions },
				{ attr: 'show_abbr', label: __( 'Show abbreviations', 'wp-club-manager' ), type: 'toggle' },
				{ attr: 'show_thumb', label: __( 'Show club badges', 'wp-club-manager' ), type: 'toggle' },
				{ attr: 'show_team', label: __( 'Show team', 'wp-club-manager' ), type: 'toggle' },
				{ attr: 'show_comp', label: __( 'Show competition', 'wp-club-manager' ), type: 'toggle' },
				{ attr: 'show_venue', label: __( 'Show venue', 'wp-club-manager' ), type: 'toggle' },
				{ attr: 'linktext', label: __( 'Link text', 'wp-club-manager' ) },
				{ attr: 'linkpage', label: __( 'Link page ID', 'wp-club-manager' ) },
			] ),
			save: function() { return null; },
		} );
	}
} )();
