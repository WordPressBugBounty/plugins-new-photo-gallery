<?php
/**
@package New Photo Gallery
Plugin Name: New Photo Gallery
Plugin URI: https://awplife.com/wordpress-plugins/photo-gallery-premium/
Description: new photo gallery plugin with lightbox preview for WordPress
Version: 1.5.4
Author: A WP Life
Author URI: https://awplife.com/
License: GPLv2 or later
Text Domain: new-photo-gallery
Domain Path: /languages
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('New_Photo_Gallery')) {
	// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- NPG is the plugin prefix
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- NPG is the plugin prefix

	class New_Photo_Gallery
	{

		protected $protected_plugin_api;
		protected $ajax_plugin_nonce;

		public function __construct()
		{
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants()
		{
			// Plugin Version
			define('NPG_VER', '1.5.4');

			// Plugin Text Domain
			define('NPG_TXTDM', 'new-photo-gallery');

			// Plugin Name
			define('NPG_PLUGIN_NAME', 'New Photo Gallery');

			// Plugin Slug
			define('NPG_PLUGIN_SLUG', '_light_image_gallery');

			// Plugin Directory Path
			define('NPG_PLUGIN_DIR', plugin_dir_path(__FILE__));

			// Plugin Directory URL
			define('NPG_PLUGIN_URL', plugin_dir_url(__FILE__));

		} // end of constructor function

		/**
		 * Setup the default filters and actions
		 */
		protected function _hooks()
		{

			// Load text domain
			add_action('init', array($this, '_load_textdomain'));

			// add gallery menu item, change menu filter for multisite
			add_action('admin_menu', array($this, '_npg_menus'), 101);

			// create Image Gallery Custom Post
			add_action('init', array($this, 'light_image_gallery'));

			// Add meta box to custom post
			add_action('add_meta_boxes', array($this, '_admin_add_meta_box'));

			// loaded during admin init
			add_action('admin_init', array($this, '_admin_add_meta_box'));

			add_action('wp_ajax_photo_gallery_js', array(&$this, '_ajax_light_image_gallery'));
			add_action('save_post', array(&$this, '_lg_save_settings'));

			// shortcode compatibility in Text Widgets
			add_filter('widget_text', 'do_shortcode');

			// add npg cpt shortcode column - manage_{$post_type}_posts_columns
			add_filter('manage__light_image_gallery_posts_columns', array(&$this, 'set_light_image_gallery_shortcode_column_name'));

			// add npg cpt shortcode column data - manage_{$post_type}_posts_custom_column
			add_action('manage__light_image_gallery_posts_custom_column', array(&$this, 'custom_light_image_gallery_shortcode_data'), 10, 2);

			add_action('wp_enqueue_scripts', array(&$this, 'npg_enqueue_scripts_in_header'));

			// Admin scripts
			add_action('admin_enqueue_scripts', array($this, 'npg_admin_scripts'));

		} // end of hook function

		public function npg_enqueue_scripts_in_header()
		{
			wp_enqueue_script('jquery');
		}

		// npg cpt shortcode column before date columns
		public function set_light_image_gallery_shortcode_column_name($columns)
		{
			$new = array();
			$shortcode = isset($columns['_light_image_gallery_shortcode']) ? $columns['_light_image_gallery_shortcode'] : '';
			unset($columns['tags']); // remove it from the columns list

			foreach ($columns as $key => $value) {
				if ($key == 'date') {  // when we find the date column
					$new['_light_image_gallery_shortcode'] = __('Shortcode', 'new-photo-gallery');  // put the tags column before it
				}
				$new[$key] = $value;
			}
			return $new;
		}

		// npg cpt shortcode column data
		public function custom_light_image_gallery_shortcode_data($column, $post_id)
		{
			switch ($column) {
				case '_light_image_gallery_shortcode':
					echo "<input type='text' class='button button-primary' id='light-image-gallery-shortcode-" . esc_attr($post_id) . "' value='[NPG id=" . esc_attr($post_id) . "]' style='font-weight:bold; background-color:#32373C; color:#FFFFFF; text-align:center;' />";
					echo "<input type='button' class='button button-primary' onclick='return PHOTOCopyShortcode" . esc_attr($post_id) . "();' readonly value='Copy' style='margin-left:4px;' />";
					echo "<span id='copy-msg-" . esc_attr($post_id) . "' class='button button-primary' style='display:none; background-color:#32CD32; color:#FFFFFF; margin-left:4px; border-radius: 4px;'>copied</span>";
					echo '<script>
						function PHOTOCopyShortcode' . esc_attr($post_id) . "() {
							var copyText = document.getElementById('light-image-gallery-shortcode-" . esc_attr($post_id) . "');
							copyText.select();
							document.execCommand('copy');
							
							//fade in and out copied message
							jQuery('#copy-msg-" . esc_attr($post_id) . "').fadeIn('1000', 'linear');
							jQuery('#copy-msg-" . esc_attr($post_id) . "').fadeOut(2500,'swing');
						}
						</script>
					";
					break;
			}
		}

		// Loads the language file
		public function _load_textdomain()
		{
			load_plugin_textdomain('new-photo-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages');
		}

		// Adds the photo gallery menus
		public function _npg_menus()
		{
			$themes_menu = add_submenu_page('edit.php?post_type=' . NPG_PLUGIN_SLUG, __('Our Themes', 'new-photo-gallery'), __('Our Themes', 'new-photo-gallery'), 'administrator', 'npg-themes', array($this, '_npg_theme_page'));
			$plugins_menu = add_submenu_page('edit.php?post_type=' . NPG_PLUGIN_SLUG, __('Our Plugins', 'new-photo-gallery'), __('Our Plugins', 'new-photo-gallery'), 'administrator', 'npg-plugins', array($this, '_npg_featured_plugins'));
		}

		// Photo Gallery Custom Post
		public function light_image_gallery()
		{
			$labels = array(
				'name' => __('New Photo Gallery', 'new-photo-gallery'),
				'singular_name' => __('New Photo Gallery', 'new-photo-gallery'),
				'menu_name' => __('New Photo Gallery', 'new-photo-gallery'),
				'name_admin_bar' => __('New Photo Gallery', 'new-photo-gallery'),
				'parent_item_colon' => __('Parent Item:', 'new-photo-gallery'),
				'all_items' => __('All Photo Gallery', 'new-photo-gallery'),
				'add_new_item' => __('Add New Photo Gallery', 'new-photo-gallery'),
				'add_new' => __('Add New Gallery', 'new-photo-gallery'),
				'new_item' => __('New Photo Gallery', 'new-photo-gallery'),
				'edit_item' => __('Edit New Photo Gallery', 'new-photo-gallery'),
				'update_item' => __('Update New Photo Gallery', 'new-photo-gallery'),
				'search_items' => __('Search New Photo Gallery', 'new-photo-gallery'),
				'not_found' => __('Photo Gallery Not found', 'new-photo-gallery'),
				'not_found_in_trash' => __('Photo Gallery Not found in Trash', 'new-photo-gallery'),
			);
			$args = array(
				'label' => __('New Photo Gallery', 'new-photo-gallery'),
				'label' => __('New Photo Gallery', 'new-photo-gallery'),
				'description' => __('Custom Post Type For New Photo Gallery', 'new-photo-gallery'),
				'labels' => $labels,
				'supports' => array('title'),
				'taxonomies' => array(),
				'hierarchical' => false,
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 65,
				'menu_icon' => 'dashicons-images-alt2',
				'show_in_admin_bar' => true,
				'show_in_nav_menus' => true,
				'can_export' => true,
				'has_archive' => true,
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'capability_type' => 'page',
			);
			register_post_type('_light_image_gallery', $args);

		} // end of post type function

		// gallery setting meta box
		public function _admin_add_meta_box()
		{
			// Syntax: add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
			add_meta_box('1', __('Copy Photo Gallery Shortcode', 'new-photo-gallery'), array(&$this, '_lg_shortcode_left_metabox'), '_light_image_gallery', 'side', 'default');
			add_meta_box('', __('Add Photos To Photo Gallery', 'new-photo-gallery'), array(&$this, 'lg_upload_multiple_images'), '_light_image_gallery', 'normal', 'default');
		}

		// image gallery copy shortcode meta box under publish button
		public function _lg_shortcode_left_metabox($post)
		{ ?>
			<p class="input-text-wrap">
				<input type="text" name="photoCopyShortcode" id="photoCopyShortcode"
					value="<?php echo '[NPG id=' . esc_attr($post->ID) . ']'; ?>" readonly
					style="height: 50px; text-align: center; width:100%;  font-size: 24px; border: 2px dashed;">
			<p id="npg-copy-code"><?php esc_html_e('Shortcode copied to clipboard!', 'new-photo-gallery'); ?></p>
			<p style="margin-top: 10px">
				<?php esc_html_e('Copy & Embed shotcode into any Page/ Post / Text Widget to display gallery.', 'new-photo-gallery'); ?>
			</p>
			</p>
			<span onclick="copyToClipboard('#photoCopyShortcode')" class="npg-copy dashicons dashicons-clipboard"></span>
			<style>
				.npg-copy {
					position: absolute;
					top: 9px;
					right: 24px;
					font-size: 26px;
					cursor: pointer;
				}

				.ui-sortable-handle>span {
					font-size: 16px !important;
				}
			</style>
			<script>
				jQuery("#npg-copy-code").hide();
				function copyToClipboard(element) {
					var $temp = jQuery("<input>");
					jQuery("body").append($temp);
					$temp.val(jQuery(element).val()).select();
					document.execCommand("copy");
					$temp.remove();
					jQuery("#photoCopyShortcode").select();
					jQuery("#npg-copy-code").fadeIn();
				}
			</script>
			<?php
		}



		public function lg_upload_multiple_images($post)
		{
			?>
			<div id="photo-gallery" class="npg-uploader-wrapper">

				<!-- Toolbar -->
				<div class="npg-toolbar">
					<div id="add-new-photos" class="button button-primary npg-add-btn">
						<span class="dashicons dashicons-plus"></span> <?php esc_html_e('Add Photos', 'new-photo-gallery'); ?>
						<?php wp_nonce_field('lg_add_images', 'lg_add_images_nonce'); ?>
					</div>

					<input type="button" id="remove-all-photos" name="remove-all-photos" class="button button-link-delete" rel=""
						value="<?php esc_html_e('Delete All Photos', 'new-photo-gallery'); ?>">
				</div>

				<ul id="remove-photos" class="npg-photo-grid photo-box">
					<?php

					$gallery_settings = get_post_meta($post->ID, 'awl_lg_settings_' . $post->ID, true);


					if (isset($gallery_settings['slide-ids'])) {
						$count = 0;
						foreach ($gallery_settings['slide-ids'] as $id) {
							$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
							$attachment = get_post($id);
							$image_link = isset($gallery_settings['slide-link'][$count]) ? $gallery_settings['slide-link'][$count] : '';
							$image_type = isset($gallery_settings['slide-type'][$count]) ? $gallery_settings['slide-type'][$count] : 'image';
							?>
							<li class="npg-photo-card">
								<div class="npg-photo-preview">
									<img class="photo" src="<?php echo esc_url($thumbnail[0]); ?>"
										alt="<?php echo esc_html(get_the_title($id)); ?>">
									<div class="npg-card-actions">
										<input type="button" name="remove-photo" id="remove-photo" class="button button-icon-delete"
											title="Delete" value="&times;">
									</div>
								</div>

								<div class="npg-card-content">
									<input type="hidden" id="slide-ids[]" name="slide-ids[]" value="<?php echo esc_attr($id); ?>" />

									<!-- Type -->
									<div class="npg-field-group">
										<select id="slide-type[]" name="slide-type[]" class="npg-input-sm photo-type">
											<option value="image" <?php selected($image_type, 'image'); ?>>
												<?php esc_html_e('Image', 'new-photo-gallery'); ?>
											</option>
											<option value="video" <?php selected($image_type, 'video'); ?>>
												<?php esc_html_e('Video', 'new-photo-gallery'); ?>
											</option>
										</select>
									</div>

									<!-- Title -->
									<div class="npg-field-group">
										<input type="text" name="slide-title[]" id="slide-title[]" class="npg-input-sm photo-title"
											placeholder="<?php esc_html_e('Title', 'new-photo-gallery'); ?>"
											value="<?php echo esc_attr(get_the_title($id)); ?>">
									</div>

									<!-- Link -->
									<div class="npg-field-group">
										<input type="text" name="slide-link[]" id="slide-link[]" class="npg-input-sm photo-link"
											placeholder="<?php esc_html_e('Video URL', 'new-photo-gallery'); ?>"
											value="<?php echo esc_attr($image_link); ?>">
									</div>
								</div>
							</li>
							<?php
							$count++;
						} // end of for each
					} //end of if
					?>
				</ul>
			</div>
			<div style="clear:left;"></div>
			<br>
			<h1><?php esc_html_e('Configure Settings For Photo Gallery', 'new-photo-gallery'); ?></h1>
			<hr>
			<?php
			require_once 'settings.php';
		} // end of upload multiple image

		public function _ajax_light_image_gallery()
		{
			if (current_user_can('manage_options')) {
				$nonce = isset($_POST['lg_add_images_nonce']) ? sanitize_text_field(wp_unslash($_POST['lg_add_images_nonce'])) : '';
				if (wp_verify_nonce($nonce, 'lg_add_images')) {
					$slide_id = isset($_POST['slideId']) ? absint(wp_unslash($_POST['slideId'])) : 0;
					$this->_lg_ajax_callback_function($slide_id);
					wp_die();
				} else {
					print 'Sorry, your nonce did not verify.';
					exit;
				}
			}
		}

		public function _lg_ajax_callback_function($id)
		{
			$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
			$attachment = get_post($id); // $id = attachment id
			?>
			<li class="npg-photo-card">
				<div class="npg-photo-preview">
					<img class="photo" src="<?php echo esc_url($thumbnail[0]); ?>"
						alt="<?php echo esc_html(get_the_title($id)); ?>">
					<div class="npg-card-actions">
						<input type="button" name="remove-photo" id="remove-photo" class="button button-icon-delete" title="Delete"
							value="&times;">
					</div>
				</div>

				<div class="npg-card-content">
					<input type="hidden" id="slide-ids[]" name="slide-ids[]" value="<?php echo esc_attr($id); ?>" />

					<!-- Type -->
					<div class="npg-field-group">
						<?php $image_type = isset($image_type) ? $image_type : 'image'; ?>
						<select id="slide-type[]" name="slide-type[]" class="npg-input-sm photo-type">
							<option value="image" <?php selected($image_type, 'image'); ?>>
								<?php esc_html_e('Image', 'new-photo-gallery'); ?>
							</option>
							<option value="video" <?php selected($image_type, 'video'); ?>>
								<?php esc_html_e('Video', 'new-photo-gallery'); ?>
							</option>
						</select>
					</div>

					<!-- Title -->
					<div class="npg-field-group">
						<input type="text" name="slide-title[]" id="slide-title[]" class="npg-input-sm photo-title"
							placeholder="<?php esc_html_e('Title', 'new-photo-gallery'); ?>"
							value="<?php echo esc_attr(get_the_title($id)); ?>">
					</div>

					<!-- Link -->
					<div class="npg-field-group">
						<input type="text" name="slide-link[]" id="slide-link[]" class="npg-input-sm photo-link"
							placeholder="<?php esc_html_e('Video URL', 'new-photo-gallery'); ?>">
					</div>
				</div>
			</li>
			<?php
		}

		public function _lg_save_settings($post_id)
		{
			if (current_user_can('manage_options')) {
				$nonce = isset($_POST['lg_save_nonce']) ? sanitize_text_field(wp_unslash($_POST['lg_save_nonce'])) : '';
				if (wp_verify_nonce($nonce, 'lg_save_settings')) {

					$gal_thumb_size = isset($_POST['gal_thumb_size']) ? sanitize_text_field(wp_unslash($_POST['gal_thumb_size'])) : '';
					$col_large_desktops = isset($_POST['col_large_desktops']) ? sanitize_text_field(wp_unslash($_POST['col_large_desktops'])) : '';
					$col_desktops = isset($_POST['col_desktops']) ? sanitize_text_field(wp_unslash($_POST['col_desktops'])) : '';
					$col_tablets = isset($_POST['col_tablets']) ? sanitize_text_field(wp_unslash($_POST['col_tablets'])) : '';
					$col_phones = isset($_POST['col_phones']) ? sanitize_text_field(wp_unslash($_POST['col_phones'])) : '';
					$tool_color = isset($_POST['tool_color']) ? sanitize_text_field(wp_unslash($_POST['tool_color'])) : '';
					$title_color = isset($_POST['title_color']) ? sanitize_text_field(wp_unslash($_POST['title_color'])) : '';
					$image_hover_effect_type = isset($_POST['image_hover_effect_type']) ? sanitize_text_field(wp_unslash($_POST['image_hover_effect_type'])) : '';
					$image_hover_effect_four = isset($_POST['image_hover_effect_four']) ? sanitize_text_field(wp_unslash($_POST['image_hover_effect_four'])) : '';
					$transition_effects = isset($_POST['transition_effects']) ? sanitize_text_field(wp_unslash($_POST['transition_effects'])) : '';
					$thumbnails_spacing = isset($_POST['thumbnails_spacing']) ? sanitize_text_field(wp_unslash($_POST['thumbnails_spacing'])) : '';
					$custom_css = isset($_POST['custom_css']) ? sanitize_textarea_field(wp_unslash($_POST['custom_css'])) : '';

					$i = 0;
					$image_ids = array();
					$image_titles = array();
					$image_type = array();
					$slide_link = array();
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below with array_map
					$image_ids_val = isset($_POST['slide-ids']) ? array_map('absint', wp_unslash((array) $_POST['slide-ids'])) : array();

					foreach ($image_ids_val as $image_id) {
						$image_ids[] = $image_id;
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Values are accessed by index
						$image_titles[] = isset($_POST['slide-title'][$i]) ? sanitize_text_field(wp_unslash($_POST['slide-title'][$i])) : '';
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Values are accessed by index
						$image_type[] = isset($_POST['slide-type'][$i]) ? sanitize_text_field(wp_unslash($_POST['slide-type'][$i])) : '';
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Values are accessed by index
						$slide_link[] = isset($_POST['slide-link'][$i]) ? esc_url_raw(wp_unslash($_POST['slide-link'][$i])) : '';
						$single_image_update = array(
							'ID' => $image_id,
							'post_title' => $image_titles[$i],
						);
						// wp_update_post($single_image_update); // Removing this as it might not be intended to update the attachment title on every save, and was potentially causing issues or was redundant. If needed, uncomment.
						$i++;
					}

					$image_protection = isset($_POST['image_protection']) ? sanitize_text_field(wp_unslash($_POST['image_protection'])) : '';
					$image_grayscale = isset($_POST['image_grayscale']) ? sanitize_text_field(wp_unslash($_POST['image_grayscale'])) : '';

					$gallery_settings = array(
						'slide-ids' => $image_ids,
						'slide-title' => $image_titles,
						'slide-type' => $image_type,
						'slide-link' => $slide_link,
						'gal_thumb_size' => $gal_thumb_size,
						'col_large_desktops' => $col_large_desktops,
						'col_desktops' => $col_desktops,
						'col_tablets' => $col_tablets,
						'col_phones' => $col_phones,
						'tool_color' => $tool_color,
						'title_color' => $title_color,
						'image_hover_effect_type' => $image_hover_effect_type,
						'image_hover_effect_four' => $image_hover_effect_four,
						'transition_effects' => $transition_effects,
						'thumbnails_spacing' => $thumbnails_spacing,
						'image_protection' => $image_protection,
						'image_grayscale' => $image_grayscale,
						'image_grayscale' => $image_grayscale,
						'custom_css' => $custom_css,

					);
					$awl_light_image_gallery_shortcode_setting = 'awl_lg_settings_' . $post_id;
					update_post_meta($post_id, $awl_light_image_gallery_shortcode_setting, $gallery_settings);
				}
			}
		}//end _lg_save_settings()


		// a wp life plugins page
		public function _npg_featured_plugins()
		{
			require_once 'our-plugins/awplife-plugins.php';
		}

		// a wp life themes page
		public function _npg_theme_page()
		{
			require_once 'our-themes/awplife-themes.php';
		}
		// Admin scripts
		public function npg_admin_scripts($hook)
		{
			global $post;

			if ($hook == 'post-new.php' || $hook == 'post.php') {
				if ('_light_image_gallery' === $post->post_type) {
					wp_enqueue_script('media-upload');
					wp_enqueue_script('awplife-npg-uploader-js', NPG_PLUGIN_URL . 'js/awplife-npg-uploader.js', array('jquery'), NPG_VER, true);
					wp_enqueue_style('awplife-npg-uploader-css', NPG_PLUGIN_URL . 'css/awplife-npg-uploader.css', array(), '1.5.6');
					wp_enqueue_media();

					// Admin Layout CSS
					wp_enqueue_style('npg-admin-css', NPG_PLUGIN_URL . 'css/npg-admin.css', array(), '1.5.6');
				}
			}
		}

	}//end class

	// register sf scripts
	function npg_register_scripts()
	{

		// css & JS
		wp_register_script('npg-ig-bootstrap-js', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), NPG_VER, true);
		wp_register_script('awplife-npg-isotope-js', plugin_dir_url(__FILE__) . 'js/isotope.pkgd.js', array(), NPG_VER, true);
		wp_register_style('npg-bootstrap-css', plugin_dir_url(__FILE__) . 'css/bootstrap.css', array(), NPG_VER);
		// css & JS
	}
	add_action('wp_enqueue_scripts', 'npg_register_scripts');


	// Plugin Recommend
	add_action('tgmpa_register', 'npg_plugin_recommend');
	function npg_plugin_recommend()
	{
		$plugins = array(
			array(
				'name' => 'Modal Popup Box',
				'slug' => 'modal-popup-box',
				'required' => false,
			),
			array(
				'name' => 'Animated Live Wall',
				'slug' => 'animated-live-wall',
				'required' => false,
			),
			array(
				'name' => 'Album Gallery Photostream Profile For Flickr',
				'slug' => 'wp-flickr-gallery',
				'required' => false,
			),
		);
		tgmpa($plugins);
	}

	/**
	 * Instantiates the Class
	 */
	$npg_gallery_object = new New_Photo_Gallery();
	require_once 'shortcode.php';
	require_once 'class-tgm-plugin-activation.php';
} // end of class exists
?>