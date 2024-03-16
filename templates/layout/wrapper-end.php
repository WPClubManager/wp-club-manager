<?php
/**
 * Content wrappers
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.0.0
 */

$template = get_option( 'template' );

switch ( $template ) {
	case 'twentyeleven':
		echo '</div>';
		get_sidebar();
		echo '</div>';
		break;
	case 'twentytwelve':
		echo '</div></div>';
		break;
	case 'twentythirteen':
		echo '</div></div>';
		break;
	case 'twentyfourteen':
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentyfifteen':
		echo '</div></div>';
		break;
	case 'twentysixteen':
		echo '</main></div>';
		break;
	case 'twentyseventeen':
		echo '</main>';
		echo '</div>';
		get_sidebar();
		echo '</div>';
		break;
	case 'twentynineteen':
		echo '</section>';
		echo '</main';
		break;
	case 'twentytwenty':
		echo '</section>';
		echo '</main>';
		break;
	default:
		// echo '</div></div>';
		echo '</main></div>';
		break;
}
