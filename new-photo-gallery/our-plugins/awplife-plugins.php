<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- NPG is the plugin prefix
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- NPG is the plugin prefix
wp_enqueue_style('awplife-npg-bootstrap-css', NPG_PLUGIN_URL.'our-plugins/css/bootstrap.css', array(), NPG_VER);
wp_enqueue_style('awplife-npg-smartech-css', NPG_PLUGIN_URL.'our-plugins/css/smartech.css', array(), NPG_VER);
wp_enqueue_style('awplife-npg-feature-plugins', NPG_PLUGIN_URL.'our-plugins/css/feature-plugins.css', array(), NPG_VER);
?>
<style>
.col-md-12{
	background-color: #073b4c !important;
	color: #ffffff !important;
	font-family: initial !important;
	font-size: 18px !important;
	width: 100% !important;
}
.welcome-panel p {
	color: #ecf1f1 !important;
}
.desc p {
	color: #ffffff !important;
	font-size: 19px !important;
	line-height: 1.5 !important;
}
.thickbox {
	color: #177EE5 !important;
	font-size: 22px;
}
.authors a{
	color: #F0A81E !important;
}
.hndle span {
	color: #ffffff !important;
	font-family: georgia !important;
	line-height: 1.4 !important;
	margin: 0 !important;
	padding: 8px 12px !important;
}
<!--buy buttons setting-->
.btn-info {
	height: 20%;
	width: 10%
}
</style>
<?php
		include( ABSPATH . "wp-admin/includes/plugin-install.php" );
		global $tabs, $tab, $paged, $type, $term;
		$tabs = array();
		$tab = "search";
		$per_page = 50;
		$npg_args = array
		(
			"author"=> "awordpresslife",
			"page" => $paged,
			"per_page" => $per_page,
			"fields" => array( "last_updated" => true, "active_installs" => true, "downloaded" => true, "icons" => true, ),
			"locale" => get_locale(),
		);
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress hook pattern
		$npg_arges = apply_filters( "install_plugins_table_api_args_$tab", $npg_args );
		$npg_api = plugins_api( "query_plugins", $npg_arges );
		$npg_item = $npg_api->plugins;
		if(!function_exists("npg_wp_star_rating"))
		{
			function npg_wp_star_rating( $npg_args = array() )
			{
				$defaults = array(
						'rating' => 0,
						'type' => 'rating',
						'number' => 0,
				);
				$r = wp_parse_args( $npg_args, $defaults );
		
				// Non-english decimal places when the $rating is coming from a string
				$rating = str_replace( ',', '.', $r['rating'] );
		
				// Convert Percentage to star rating, 0..5 in .5 increments
				if ( 'percent' == $r['type'] ) {
					$rating = round( $rating / 10, 0 ) / 2;
				}
		
				// Calculate the number of each type of star needed
				$full_stars = floor( $rating );
				$half_stars = ceil( $rating - $full_stars );
				$empty_stars = 5 - $full_stars - $half_stars;
		
				if ( $r['number'] ) {
					/* translators: 1: The rating, 2: The number of ratings */
					$format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'], 'new-photo-gallery' );
					$title = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
				} else {
					/* translators: 1: The rating */
					$title = sprintf( __( '%s rating', 'new-photo-gallery' ), number_format_i18n( $rating, 1 ) );
				}
		
				echo '<div class="star-rating" title="' . esc_attr( $title ) . '">';
				echo '<span class="screen-reader-text">' . esc_html( $title ) . '</span>';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is hardcoded HTML
				echo str_repeat( '<div class="star star-full"></div>', $full_stars );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is hardcoded HTML
				echo str_repeat( '<div class="star star-half"></div>', $half_stars );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is hardcoded HTML
				echo str_repeat( '<div class="star star-empty"></div>', $empty_stars);
				echo '</div>';
			}
		}
	?>
	

<div class="wrap">
	<h2 class="hndle text-center" style="background-color: #073b4c;"><span>Get More Free WordPress Plugins From A WP Life</span></h2>	
	<div id="welcome-panel" class="welcome-panel">
		<div class="container">
			<div class="row">
				<table class="form-table2">
					<tr class="radio-span" style="border-bottom:none;">
						<td><?php
							foreach ((array) $npg_item as $plugin) 
							{
								if (is_object( $plugin))
								{
									$plugin = (array) $plugin;
									
								}
								if (!empty($plugin["icons"]["svg"]))
								{
									$npg_plugin_icon_url = $plugin["icons"]["svg"];
								} 
								elseif (!empty( $plugin["icons"]["2x"])) 
								{
									$npg_plugin_icon_url = $plugin["icons"]["2x"];
								} 
								elseif (!empty( $plugin["icons"]["1x"]))
								{
									$npg_plugin_icon_url = $plugin["icons"]["1x"];
								} 
								else 
								{
									$npg_plugin_icon_url = $plugin["icons"]["default"];
								}
								$npg_plugins_allowedtags = array
								(
									"a" => array( "href" => array(),"title" => array(), "target" => array() ),
									"abbr" => array( "title" => array() ),"acronym" => array( "title" => array() ),
									"code" => array(), "pre" => array(), "em" => array(),"strong" => array(),
									"ul" => array(), "ol" => array(), "li" => array(), "p" => array(), "br" => array()
								);
								$title = wp_kses($plugin["name"], $npg_plugins_allowedtags);
								$npg_description = wp_strip_all_tags($plugin["short_description"]);
								$npg_author = wp_kses($plugin["author"], $npg_plugins_allowedtags);
								$npg_version = wp_kses($plugin["version"], $npg_plugins_allowedtags);
								$npg_name = wp_strip_all_tags( $title . " " . $npg_version );
								$npg_details_link   = self_admin_url( "plugin-install.php?tab=plugin-information&amp;plugin=" . $plugin["slug"] .
								"&amp;TB_iframe=true&amp;width=600&amp;height=550" );
								
								/* translators: 1: Plugin name and version. */
								$npg_action_links[] = '<a href="' . esc_url( $npg_details_link ) . '" class="thickbox" aria-label="' . esc_attr( sprintf( __( 'More information about %s', 'new-photo-gallery' ), $npg_name ) ) . '" data-title="' . esc_attr( $npg_name ) . '">' . __( 'More Details', 'new-photo-gallery' ) . '</a>';
								$npg_action_links = array();
								if (current_user_can( "install_plugins") || current_user_can("update_plugins"))
								{
									$status = install_plugin_install_status( $plugin );
									switch ($status["status"])
									{
										case "install":
											if ( $status["url"] )
											{
												/* translators: 1: Plugin name and version. */
												$npg_action_links[] = '<a class="install-now button button-primary" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Install %s now', 'new-photo-gallery' ), $npg_name ) ) . '">' . __( 'Install Now', 'new-photo-gallery' ) . '</a>';
											}
										break;
										case "update_available":
											if ($status["url"])
											{
												/* translators: 1: Plugin name and version */
												$npg_action_links[] = '<a class="button button-primary" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Update %s now', 'new-photo-gallery' ), $npg_name ) ) . '">' . __( 'Update Now', 'new-photo-gallery' ) . '</a>';
											}
										break;
										case "latest_installed":
										case "newer_installed":
											$npg_action_links[] = '<button class="button button-primary button-disabled" title="' . esc_attr__( 'This plugin is already installed and is up to date', 'new-photo-gallery' ) . ' ">' . __( 'Installed', 'new-photo-gallery' ) . '</button>';
										break;
									}
								}
								?>
								<div class="col-md-12">
									<br>
									<div class="col-md-2">
										<div class="text-center">
											<a href="<?php echo esc_url( $npg_details_link ); ?>">
												<img class="custom_icon" src="<?php echo esc_attr( $npg_plugin_icon_url ) ?>" />
											</a>
										</div><br>
										<div class="text-center">
											<ul>
												<?php
													if ($npg_action_links)
													{
														echo wp_kses_post( implode("", $npg_action_links) );
													}
														?>		
											</ul>
										</div>
									</div>	
									
									<div class="col-md-8">
										<div class="text-center plugin-div-inner-content">
											<div class="name column-name">
												<h4>
													<a href="<?php echo esc_url( $npg_details_link ); ?>" class="thickbox"><?php echo esc_html( $title ); ?></a>
												</h4>
											</div>
											<div class="desc column-description">
												<p>
													<?php echo esc_html( $npg_description ); ?>
												</p>
												<p class="authors">
													<cite>
														By <?php echo wp_kses_post( $npg_author ); ?>
													</cite>
												</p>
											</div>
										</div>
									</div>
									<style>
										table .column-rating, table .column-visible, table .vers {
											text-align: center !important;
										}
									</style>
									<div class="col-md-2 text-center">
										<div class="plugin-div-inner-content">
											<div class="name column-name">
												<div class="vers column-rating">
													<?php wp_star_rating( array( "rating" => $plugin["rating"], "type" => "percent", "number" => $plugin["num_ratings"] ) ); ?>
													<span class="num-ratings">
														(<?php echo esc_html( number_format_i18n( $plugin["num_ratings"] ) ); ?>)
													</span>
												</div>
												<div class="column-updated">
													<strong><?php esc_html_e( 'Last Updated:', 'new-photo-gallery' ); ?></strong> <span title="<?php echo esc_attr($plugin["last_updated"]); ?>">
														<?php
														/* translators: %s: Human-readable time difference */
														printf( esc_html__( '%s ago', 'new-photo-gallery' ), esc_html( human_time_diff( strtotime( $plugin["last_updated"] ) ) ) );
														?>
													</span>
												</div>
												<div class="column-downloaded">
													<?php
													/* translators: %s: Number of downloads */
													echo esc_html( sprintf( _n( '%s download', '%s downloads', $plugin['downloaded'], 'new-photo-gallery' ), number_format_i18n( $plugin['downloaded'] ) ) );
													?>
												</div>
											</div>
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-md-12 text-center custom-buy-button">
											<a href="https://awplife.com/wordpress-premium-plugins/" target="_blank" class="button button-hero button-primary">Buy Now</a>
										</div>
									</div>
									<hr>
								</div>
								<hr>
										<?php
							}
										?>
						</td>
					</tr>
				</table>
				<hr>
			</div> 
		</div>
	</div>
</div>