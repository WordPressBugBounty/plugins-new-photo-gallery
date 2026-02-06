<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Load Settings
$post_id = esc_attr($post->ID);
$gallery_settings = get_post_meta($post->ID, 'awl_lg_settings_' . $post->ID, true);

// Defaults
$defaults = array(
	'gal_thumb_size' => 'full',
	'col_large_desktops' => 'col-lg-4',
	'col_desktops' => 'col-md-4',
	'col_tablets' => 'col-sm-4',
	'col_phones' => 'col-xs-6',
	'tool_color' => 'gold',
	'title_color' => 'white',
	'image_hover_effect_type' => 'no',
	'image_hover_effect_four' => 'hvr-box-shadow-outset',
	'transition_effects' => 'lg-fade',
	'image_protection' => 1,
	'image_grayscale' => 0,
	'thumbnails_spacing' => 0,
	'custom_css' => '',
);

// Merge saved settings with defaults
$settings = wp_parse_args($gallery_settings, $defaults);

?>

<div class="npg-settings-wrapper">
	<?php wp_nonce_field('lg_save_settings', 'lg_save_nonce'); ?>

	<!-- Layout Settings -->
	<div class="npg-card">
		<div class="npg-card-header">
			<h3 class="npg-card-title"><i class="dashicons dashicons-layout"></i>
				<?php esc_html_e('Layout Settings', 'new-photo-gallery'); ?></h3>
		</div>
		<div class="npg-card-body">
			<div class="npg-row">
				<div class="npg-col">
					<div class="npg-form-group">
						<label class="npg-label"
							for="gal_thumb_size"><?php esc_html_e('Thumbnail Size', 'new-photo-gallery'); ?></label>
						<select id="gal_thumb_size" name="gal_thumb_size" class="npg-select">
							<option value="thumbnail" <?php selected($settings['gal_thumb_size'], 'thumbnail'); ?>>
								Thumbnail - 150 x 150</option>
							<option value="medium" <?php selected($settings['gal_thumb_size'], 'medium'); ?>>Medium -
								300 x 169</option>
							<option value="large" <?php selected($settings['gal_thumb_size'], 'large'); ?>>Large - 840
								x 473</option>
							<option value="full" <?php selected($settings['gal_thumb_size'], 'full'); ?>>Full Size -
								1280 x 720</option>
						</select>
						<p class="npg-help-text">
							<?php esc_html_e('Select the size of thumbnails to display in the gallery grid.', 'new-photo-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="npg-col">
					<div class="npg-form-group">
						<label class="npg-label"><?php esc_html_e('Thumbnails Spacing', 'new-photo-gallery'); ?></label>
						<div class="npg-switch-field">
							<input type="radio" id="spacing_yes" name="thumbnails_spacing" value="1" <?php checked($settings['thumbnails_spacing'], 1); ?> />
							<label for="spacing_yes"><?php esc_html_e('Show Gap', 'new-photo-gallery'); ?></label>
							<input type="radio" id="spacing_no" name="thumbnails_spacing" value="0" <?php checked($settings['thumbnails_spacing'], 0); ?> />
							<label for="spacing_no"><?php esc_html_e('No Gap', 'new-photo-gallery'); ?></label>
						</div>
						<p class="npg-help-text">
							<?php esc_html_e('Enable or disable spacing between gallery items.', 'new-photo-gallery'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="npg-form-group">
				<label class="npg-label"><?php esc_html_e('Column Layout', 'new-photo-gallery'); ?></label>
				<div class="npg-row">
					<div class="npg-col">
						<label class="npg-help-text"><?php esc_html_e('Large Desktop', 'new-photo-gallery'); ?></label>
						<select name="col_large_desktops" class="npg-select">
							<?php
							$cols = array('col-lg-12' => '1 Column', 'col-lg-6' => '2 Columns', 'col-lg-4' => '3 Columns', 'col-lg-3' => '4 Columns', 'col-lg-2' => '6 Columns', 'col-lg-1' => '12 Columns');
							foreach ($cols as $val => $label) {
								echo '<option value="' . esc_attr($val) . '" ' . selected($settings['col_large_desktops'], $val, false) . '>' . esc_html($label) . '</option>';
							}
							?>
						</select>
					</div>
					<div class="npg-col">
						<label class="npg-help-text"><?php esc_html_e('Desktop', 'new-photo-gallery'); ?></label>
						<select name="col_desktops" class="npg-select">
							<?php
							$cols_md = array('col-md-12' => '1 Column', 'col-md-6' => '2 Columns', 'col-md-4' => '3 Columns', 'col-md-3' => '4 Columns', 'col-md-2' => '6 Columns', 'col-md-1' => '12 Columns');
							foreach ($cols_md as $val => $label) {
								echo '<option value="' . esc_attr($val) . '" ' . selected($settings['col_desktops'], $val, false) . '>' . esc_html($label) . '</option>';
							}
							?>
						</select>
					</div>
					<div class="npg-col">
						<label class="npg-help-text"><?php esc_html_e('Tablets', 'new-photo-gallery'); ?></label>
						<select name="col_tablets" class="npg-select">
							<?php
							$cols_sm = array('col-sm-12' => '1 Column', 'col-sm-6' => '2 Columns', 'col-sm-4' => '3 Columns', 'col-sm-3' => '4 Columns', 'col-sm-2' => '6 Columns');
							foreach ($cols_sm as $val => $label) {
								echo '<option value="' . esc_attr($val) . '" ' . selected($settings['col_tablets'], $val, false) . '>' . esc_html($label) . '</option>';
							}
							?>
						</select>
					</div>
					<div class="npg-col">
						<label class="npg-help-text"><?php esc_html_e('Phones', 'new-photo-gallery'); ?></label>
						<select name="col_phones" class="npg-select">
							<?php
							$cols_xs = array('col-xs-12' => '1 Column', 'col-xs-6' => '2 Columns', 'col-xs-4' => '3 Columns', 'col-xs-3' => '4 Columns');
							foreach ($cols_xs as $val => $label) {
								echo '<option value="' . esc_attr($val) . '" ' . selected($settings['col_phones'], $val, false) . '>' . esc_html($label) . '</option>';
							}
							?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>



	<!-- Effects Settings -->
	<div class="npg-card">
		<div class="npg-card-header">
			<h3 class="npg-card-title"><i class="dashicons dashicons-admin-appearance"></i>
				<?php esc_html_e('Effects & Animations', 'new-photo-gallery'); ?></h3>
		</div>
		<div class="npg-card-body">
			<div class="npg-row">
				<div class="npg-col">
					<div class="npg-form-group">
						<label class="npg-label"><?php esc_html_e('Image Hover Effect', 'new-photo-gallery'); ?></label>
						<div class="npg-switch-field">
							<input type="radio" id="hover_none" name="image_hover_effect_type" value="no" <?php checked($settings['image_hover_effect_type'], 'no'); ?> />
							<label for="hover_none"><?php esc_html_e('None', 'new-photo-gallery'); ?></label>
							<input type="radio" id="hover_sg" name="image_hover_effect_type" value="sg" <?php checked($settings['image_hover_effect_type'], 'sg'); ?> />
							<label for="hover_sg"><?php esc_html_e('Shadow & Glow', 'new-photo-gallery'); ?></label>
						</div>
					</div>
				</div>

				<div class="npg-col he_two"
					style="<?php echo ($settings['image_hover_effect_type'] == 'no') ? 'display:none;' : ''; ?>">
					<div class="npg-form-group">
						<label class="npg-label"
							for="image_hover_effect_four"><?php esc_html_e('Select Shadow Style', 'new-photo-gallery'); ?></label>
						<select name="image_hover_effect_four" id="image_hover_effect_four" class="npg-select">
							<option value="hvr-grow-shadow" <?php selected($settings['image_hover_effect_four'], 'hvr-grow-shadow'); ?>>Grow Shadow</option>
							<option value="hvr-float-shadow" <?php selected($settings['image_hover_effect_four'], 'hvr-float-shadow'); ?>>Float Shadow</option>
							<option value="hvr-glow" <?php selected($settings['image_hover_effect_four'], 'hvr-glow'); ?>>Glow</option>
							<option value="hvr-box-shadow-outset" <?php selected($settings['image_hover_effect_four'], 'hvr-box-shadow-outset'); ?>>Box Shadow Outset</option>
							<option value="hvr-box-shadow-inset" <?php selected($settings['image_hover_effect_four'], 'hvr-box-shadow-inset'); ?>>Box Shadow Inset</option>
						</select>
					</div>
				</div>

				<div class="npg-col">
					<div class="npg-form-group">
						<label class="npg-label"
							for="transition_effects"><?php esc_html_e('Lightbox Transition', 'new-photo-gallery'); ?></label>
						<select name="transition_effects" id="transition_effects" class="npg-select">
							<option value="none" <?php selected($settings['transition_effects'], 'none'); ?>>None
							</option>
							<option value="lg-slide" <?php selected($settings['transition_effects'], 'lg-slide'); ?>>
								Slide</option>
							<option value="lg-fade" <?php selected($settings['transition_effects'], 'lg-fade'); ?>>
								Fade</option>
							<option value="lg-zoom-in" <?php selected($settings['transition_effects'], 'lg-zoom-in'); ?>>Zoom In</option>
							<option value="lg-zoom-in-big" <?php selected($settings['transition_effects'], 'lg-zoom-in-big'); ?>>Zoom In (Big)</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Extra Features -->
	<div class="npg-card">
		<div class="npg-card-header">
			<h3 class="npg-card-title"><i class="dashicons dashicons-plus-alt"></i>
				<?php esc_html_e('Extra Features', 'new-photo-gallery'); ?></h3>
		</div>
		<div class="npg-card-body">
			<div class="npg-row">

				<!-- Image Protection -->
				<div class="npg-col">
					<div class="npg-form-group">
						<label class="npg-label"><?php esc_html_e('Image Protection', 'new-photo-gallery'); ?></label>
						<div class="npg-switch-field">
							<input type="radio" id="protect_yes" name="image_protection" value="1" <?php checked($settings['image_protection'], 1); ?> />
							<label for="protect_yes"><?php esc_html_e('Enable', 'new-photo-gallery'); ?></label>
							<input type="radio" id="protect_no" name="image_protection" value="0" <?php checked($settings['image_protection'], 0); ?> />
							<label for="protect_no"><?php esc_html_e('Disable', 'new-photo-gallery'); ?></label>
						</div>
						<p class="npg-help-text">
							<?php esc_html_e('Disable right-click on images to prevent downloading.', 'new-photo-gallery'); ?>
						</p>
					</div>
				</div>

				<!-- Grayscale Effect -->
				<div class="npg-col">
					<div class="npg-form-group">
						<label class="npg-label"><?php esc_html_e('Grayscale Effect', 'new-photo-gallery'); ?></label>
						<div class="npg-switch-field">
							<input type="radio" id="gray_yes" name="image_grayscale" value="1" <?php checked($settings['image_grayscale'], 1); ?> />
							<label for="gray_yes"><?php esc_html_e('Enable', 'new-photo-gallery'); ?></label>
							<input type="radio" id="gray_no" name="image_grayscale" value="0" <?php checked($settings['image_grayscale'], 0); ?> />
							<label for="gray_no"><?php esc_html_e('Disable', 'new-photo-gallery'); ?></label>
						</div>
						<p class="npg-help-text">
							<?php esc_html_e('Show images in B&W, color on hover.', 'new-photo-gallery'); ?>
						</p>
					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- Custom CSS -->
	<div class="npg-card">
		<div class="npg-card-header">
			<h3 class="npg-card-title"><i class="dashicons dashicons-editor-code"></i>
				<?php esc_html_e('Custom CSS', 'new-photo-gallery'); ?></h3>
		</div>
		<div class="npg-card-body">
			<div class="npg-form-group">
				<textarea name="custom_css" id="custom_css" class="npg-textarea"
					placeholder=".my-custom-class { color: red; }"><?php echo esc_textarea($settings['custom_css']); ?></textarea>
				<p class="npg-help-text">
					<?php esc_html_e('Enter custom CSS to override default gallery styles. Do not include <style> tags.', 'new-photo-gallery'); ?>
				</p>
			</div>
		</div>
	</div>

	<!-- Upsell -->
	<div class="npg-upsell-box">
		<h3 class="npg-upsell-title">🎁 Upgrade to Premium - Special Offer</h3>
		<p class="npg-help-text">Get all 23+ premium plugins (including Photo Gallery Premium) for just $149 (Value
			$300+)</p>
		<div style="margin-top: 15px;">
			<a href="https://awplife.com/wordpress-plugins/photo-gallery-premium/" target="_blank"
				class="npg-upsell-btn">View Premium Features</a>
			<a href="https://awplife.com/account/signup/all-premium-plugins" target="_blank" class="npg-upsell-btn">Get
				the Bundle</a>
		</div>
	</div>

</div>

<script>
	jQuery(document).ready(function ($) {
		// Hover effect toggle
		$('input[name="image_hover_effect_type"]').change(function () {
			if ($(this).val() == 'sg') {
				$('.he_two').slideDown();
			} else {
				$('.he_two').slideUp();
			}
		});

		// Initialize Color Picker if available
		if ($('.wp-color-picker').length) {
			$('.wp-color-picker').wpColorPicker();
		}
	});
</script>