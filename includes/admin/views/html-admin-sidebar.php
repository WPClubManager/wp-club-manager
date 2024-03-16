<?php
/**
 * Admin sidebar
 */

?>
<div id="postbox-container-1" class="postbox-container">
	<?php if ( wp_get_theme() != 'Victory' ) { ?>
		<div id="wpcm-support" class="stuffbox ">
			<h3>Victory Theme</h3>
			<div class="inside">
				<?php echo '<img src="' . esc_url( WPCM()->plugin_url() ) . '/assets/images/admin/wpcm-victory-preview.jpg" style="max-width:100%" />'; ?>
				<p>A clean, versatile theme, Victory integrates seamlessly with WP Club Manager to give your sports club website a fresh new look.</p>
				<p><a href="https://wpclubmanager.com/products/victory/" target="_blank">Check it out!</a></p>
				<p class="sale" style="font-size: 16px;font-weight: bold;background: #9d1919;color: #fff;text-align: center">Get 25% off with this code: <br>25PERCENT</p>
			</div>
		</div>
		<?php
	}
	?>

	<div id="wpcm-like-plugin" class="stuffbox ">
		<h3>Do You Like WP Club Manager?</h3>
		<div class="inside">
			<p>Please consider showing your appreciation by helping to spread the word.</p>
			<ul class="ul-disc">
				<li><a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/wp-club-manager?rate=5#postform">Leave a &#9733;&#9733;&#9733;&#9733;&#9733; plugin review on WordPress.org</a></li>
			</ul>
		</div>
	</div>

</div>
