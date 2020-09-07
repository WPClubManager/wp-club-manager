<?php

$shortcodes = '';

$options = array(
    'player' => array(
        'player_list', 'player_gallery'
    ),
    'staff' => array(
        'staff_list', 'staff_gallery'
    ),
    'match' => array(
        'match_opponents', 'match_list'
    ),
    'league' => array(
        'league_table'
    ),
    'maps' => array(
        'map_venue'
    ),
);

$options = apply_filters( 'wpclubmanager_shortcodes', $options );

foreach ( $options as $name => $group ) {
    if ( empty( $group ) ) continue;
    $shortcodes .= $name . '[' . implode( '|', $group ) . ']';
}

$raw = apply_filters( 'wpclubmanager_tinymce_strings', array(
    'shortcodes' =>  $shortcodes,
    'insert' =>  __( 'WP Club Manager Shortcodes', 'wp-club-manager' ),
    'match' =>  __( 'Matches', 'wp-club-manager' ),
    'player' =>  __( 'Players', 'wp-club-manager' ),
    'staff' =>  __( 'Staff', 'wp-club-manager' ),
    'league' =>  __( 'Standings', 'wp-club-manager' ),
    'maps' =>  __( 'Maps', 'wp-club-manager' ),
    'match_opponents' => __( 'Match Opponents', 'wp-club-manager' ),
    'match_list' => __( 'Match List', 'wp-club-manager' ),
    'player_list' => __( 'Player List', 'wp-club-manager' ),
    'player_gallery' => __( 'Player Gallery', 'wp-club-manager' ),
    'staff_list' => __( 'Staff List', 'wp-club-manager' ),
    'staff_gallery' => __( 'Staff Gallery', 'wp-club-manager' ),
    'league_table' => __( 'League Table', 'wp-club-manager' ),
    'map_venue' =>  __( 'Venue Map', 'wp-club-manager' ),
));

$formatted = array();

foreach ( $raw as $key => $value ) {
    $formatted[] = $key . ': "' . esc_js( $value ) . '"';
}

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
    wpclubmanager:{
        ' . implode( ', ', $formatted ) . '
    }
}})';
