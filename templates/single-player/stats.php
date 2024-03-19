<?php
/**
 * Single Player - Stats Table Wrapper
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$stats = get_wpcm_player_stats( $post->ID );
if ( is_club_mode() ) {
	$teams = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_team' );
} else {
	$teams = null;
}
$seasons = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_season' );

if ( is_array( $teams ) && count( $teams ) > 1 ) {

	foreach ( $teams as $team ) {

		$rand = rand( 1, 99999 );
		$name = $team->name;

		if ( $team->parent ) {
			$parent_team = get_term( $team->parent, 'wpcm_team' );
			$name       .= ' (' . $parent_team->name . ')';
		} ?>

		<div class="wpcm-profile-stats-block">

			<h4><?php echo esc_html( $name ); ?></h4>

			<ul class="stats-tabs-<?php echo esc_attr( $rand ); ?> stats-tabs-multi">

				<li class="tabs-multi"><a href="#wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?>"><?php /* translators: 1: season */ printf( esc_html__( 'All %s', 'wp-club-manager' ), esc_html__( 'Seasons', 'wp-club-manager' ) ); ?></a></li>

				<?php
				if ( is_array( $seasons ) ) :
					foreach ( $seasons as $season ) :
						?>

					<li><a href="#wpcm_team-<?php echo esc_attr( $team->term_id ); ?>_season-<?php echo esc_attr( $season->term_id ); ?>"><?php echo esc_attr( $season->name ); ?></a></li>

									<?php
				endforeach;
endif;
				?>

			</ul>

			<div id="wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?>" class="tabs-panel-<?php echo esc_attr( $rand ); ?> tabs-panel-multi stats-table-season-<?php echo esc_attr( $rand ); ?>">

				<?php
				wpclubmanager_get_template( 'single-player/stats-table.php', array(
					'stats'  => $stats,
					'team'   => $team->term_id,
					'season' => 0,
				) );
				?>


			</div>

			<?php
			if ( is_array( $seasons ) ) :
				foreach ( $seasons as $season ) :
					?>

				<div id="wpcm_team-<?php echo esc_attr( $team->term_id ); ?>_season-<?php echo esc_attr( $season->term_id ); ?>" class="tabs-panel-<?php echo esc_attr( $rand ); ?> tabs-panel-multi stats-table-season-<?php echo esc_attr( $rand ); ?>" style="display: none;">

									<?php
									wpclubmanager_get_template( 'single-player/stats-table.php', array(
										'stats'  => $stats,
										'team'   => $team->term_id,
										'season' => $season->term_id,
									) );
									?>



				</div>

							<?php
			endforeach;
endif;
			?>

		</div>

		<script type="text/javascript">
			(function($) {
				$('.stats-tabs-<?php echo esc_attr( $rand ); ?> a').click(function(){
					var t = $(this).attr('href');

					$(this).parent().addClass('tabs-multi <?php echo esc_attr( $rand ); ?>').siblings('li').removeClass('tabs-multi <?php echo esc_attr( $rand ); ?>');
					$(this).parent().parent().parent().find('.tabs-panel-<?php echo esc_attr( $rand ); ?>').hide();
					$(t).show();

					return false;
				});
			})(jQuery);
		</script>

		<?php
	}
} else {
	?>

	<ul class="stats-tabs">

		<li class="tabs"><a href="#wpcm_team-0_season-0"><?php /* translators: 1: season */ printf( esc_html__( 'All %s', 'wp-club-manager' ), esc_html__( 'Seasons', 'wp-club-manager' ) ); ?></a></li>

		<?php
		if ( is_array( $seasons ) ) :
			foreach ( $seasons as $season ) :
				?>

			<li><a href="#wpcm_team-0_season-<?php echo esc_attr( $season->term_id ); ?>"><?php echo esc_html( $season->name ); ?></a></li>

					<?php
		endforeach;
endif;
		?>

	</ul>

	<?php
	if ( is_array( $seasons ) ) :
		foreach ( $seasons as $season ) :
			?>

		<div id="wpcm_team-0_season-<?php echo esc_attr( $season->term_id ); ?>" class="tabs-panel" style="display: none;">

					<?php
					wpclubmanager_get_template( 'single-player/stats-table.php', array(
						'stats'  => $stats,
						'team'   => 0,
						'season' => $season->term_id,
					) );
					?>

		</div>

			<?php
	endforeach;
endif;
	?>

	<div id="wpcm_team-0_season-0" class="tabs-panel">

		<?php
		wpclubmanager_get_template( 'single-player/stats-table.php', array(
			'stats'  => $stats,
			'team'   => 0,
			'season' => 0,
		) );
		?>

	</div>

<?php } ?>
