<?php
/**
 * Content wrappers
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

switch ( $template ) {
	case 'twentyeleven':
		echo '<div id="primary"><div id="content" role="main">';
		break;
	case 'twentytwelve':
		echo '<div id="primary" class="site-content"><div id="content" role="main">';
		break;
	case 'twentythirteen':
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
		break;
	case 'twentyfourteen':
		echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwpcm">';
		break;
	case 'twentyfifteen':
		echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
		break;
	case 'twentysixteen':
		echo '<div id="primary" class="content-area"><main id="main" class="site-main" role="main">';
		break;
	case 'twentyseventeen':
		echo '<div class="wrap">';
		echo '<div id="primary" class="content-area twentyseventeen">';
		echo '<main id="main" class="site-main" role="main">';
		break;
	case 'twentynineteen':
		echo '<section id="primary" class="content-area">';
		echo '<main id="main" class="site-main">';
		break;
	case 'twentytwenty':
		echo '<section id="primary" class="content-area">';
		echo '<main id="main" class="site-main">';
		break;
	default:
		echo '<div id="primary" class="content-area"><main id="main" class="site-main" role="main">';
		break;
}
