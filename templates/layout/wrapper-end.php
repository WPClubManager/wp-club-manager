<?php
/**
 * Content wrappers
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

$template = get_option( 'template' );

switch( $template ) {
	case 'twentyeleven' :
		echo '</div></div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentyfifteen' :
		echo '</div></div>';
		break;
	case 'twentysixteen' :
		echo '</main>';
		get_sidebar( 'content-bottom' );
		echo '</div>';
		break;
	default :
		echo '</div></div>';
		break;
}