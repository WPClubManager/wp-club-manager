<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<div class="wrap wpclubmanager-extensions">
	<h1>
		<?php _e( 'WP Club Manager Extensions', 'wp-club-manager' ); ?>
		<a href="https://wpclubmanager.com/extensions/" class="page-title-action"><?php _e( 'Browse all extensions', 'wp-club-manager' ); ?></a>
		<a href="https://wpclubmanager.com/themes/" class="page-title-action"><?php _e( 'Browse themes', 'wp-club-manager' ); ?></a>
	</h1>

	<p><?php _e( 'These add-ons extend the functionality of WP Club Manager.', 'wp-club-manager' ); ?></p>

	<div class="">

		<?php if ( $extensions ) : 

			$extensions = $extensions->featured; ?>

			<ul class="extensions">

				<?php foreach ( $extensions as $extension ) {

					echo '<li class="extension">';
					echo '<a href="' . $extension->link . '">';
					echo '<img src="' . $extension->image . '"/>';
					echo '</a>';
					echo '<h3>' . $extension->title . '</h3>';
					echo '<p>' . $extension->excerpt . '</p>';
					echo '<a href="' . $extension->link . '" class="button right">Find out more</a>';
					echo '</li>';
				
				} ?>

			</ul>

		<?php else : ?>
			<p><?php printf( __( 'Our catalog of WP Club Manager Extensions can be found on wpclubmanager.com here: <a href="%s">WP Club Manager Extensions Catalog</a>', 'wp-club-manager' ), 'https://wpclubmanager.com/extensions/' ); ?></p>
		<?php endif; ?>

	</div>

</div>