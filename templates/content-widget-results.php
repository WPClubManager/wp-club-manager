<?php 

global $post;

$postid = get_the_ID();

$home_club = get_post_meta( $postid, 'wpcm_home_club', true );
$away_club = get_post_meta( $postid, 'wpcm_away_club', true );
$home_goals = get_post_meta( $postid, 'wpcm_home_goals', true );
$away_goals = get_post_meta( $postid, 'wpcm_away_goals', true );
$played = get_post_meta( $postid, 'wpcm_played', true );
$timestamp = strtotime( get_the_date() );
$gmt_offset = get_option( 'gmt_offset' );
$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$comps = get_the_terms( $postid, 'wpcm_comp' );
$teams = get_the_terms( $postid, 'wpcm_team' );

echo '<li class="fixture">';

	echo '<div class="fixture-meta">';

		if ( $show_team && is_array( $teams ) ):
			echo '<div class="team">';
			foreach ( $teams as $team ):
				echo '<span>' . $team->name . '</span>';
			endforeach;
			echo '</div>';
		endif;
		if ( $show_comp && is_array( $comps ) ):
			echo '<div class="competition">';
			foreach ( $comps as $comp ):
				echo '<span>' . $comp->name . '</span>';
			endforeach;
			echo '</div>';
		endif;

	echo '</div>';

	echo '<a href="' . get_permalink( $postid ) . '">';
		echo '<div class="clubs">';
			echo '<h4 class="home-clubs">';
				echo '<div class="home-logo">' . get_the_post_thumbnail( $home_club, 'crest-medium', array( 'title' => get_the_title( $home_club ) ) ) . '</div>';
				echo get_the_title( $home_club );
				echo '<div class="score">' . ( $played ? $home_goals : '' ) . '</div>';
			echo '</h4>';
			echo '<h4 class="away-clubs">';
				echo '<div class="away-logo">' . get_the_post_thumbnail( $away_club, 'crest-medium', array( 'title' => get_the_title( $away_club ) ) ) . '</div>';
				echo get_the_title( $away_club );
				echo '<div class="score">' . ( $played ? $away_goals : '' ) . '</div>';
			echo '</h4>';
		echo '</div>';
	echo '</a>';

	echo '<div class="wpcm-date">';
		echo '<div class="kickoff">';
			if ( $show_date )
				echo date_i18n( $date_format, $timestamp );
			if ( $show_time )
				echo ', <time>' . date_i18n( $time_format, $timestamp ) . '</time>';
		echo '</div>';			
	echo '</div>';

echo '</li>';