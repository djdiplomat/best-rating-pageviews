<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Settings Page
*
* @link			https://icopydoc.ru/
* @since		2.1.0
*/

class BRPV_Settings_Page {
	private $feed_id;
	private $feedback;

	public function __construct() {
		$this->feedback = new BRPV_Feedback();

		$this->init_hooks(); // подключим хуки
		$this->listen_submit();

		$this->get_html_form();	
	}

	public function get_html_form() { ?>
		<div class="wrap">
  			<h1>Best Rating & Pageviews</h1>
			<div id="poststuff">

				<div id="post-body" class="columns-2">

					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox">
								<h2 class="hndle"><?php _e('Clear all statistics', 'brpv'); ?>!</h2>
								<div class="inside">
									<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
										<?php wp_nonce_field('brpv_nonce_action_clear_stat', 'brpv_nonce_clear_stat_field'); ?>
										<input id="brpv_submit_clear_stat" class="button" type="submit" name="brpv_submit_clear_stat" value="<?php _e('Clear statistics', 'brpv'); ?>" />
									</form>
								</div>
							</div>
							<?php $this->feedback->get_block_support_project(); ?>
						</div>
					</div><!-- /postbox-container-1 -->

					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables"><?php 
							if (isset($_GET['tab'])) {$tab = $_GET['tab'];} else {$tab = 'main_tab';}
							echo $this->get_html_tabs($tab); ?>

							<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
								<?php do_action('brpv_prepend_form_container_2'); ?>
								<?php switch ($tab) : 
									case 'main_tab' : ?>
										<?php $this->get_html_main_settings(); ?>
										<?php break; ?>
								<?php endswitch; ?>
								<div class="postbox">
									<h2 class="hndle"><?php _e('Examples shotcodes', 'brpv'); ?></h2>	
									<div class="inside">
										<p><strong><?php _e('To display the rating stars', 'brpv'); ?></strong></p>
										<p><strong>[pageratings]</strong><br /><?php _e('or in php code', 'brpv'); ?>:<br /> 
										<strong>do_shortcode('[pageratings]');</strong></p>		
										<p><strong><?php _e('To display the number of page views', 'brpv'); ?></strong></p>
										<p><strong>[pageviews]</strong><br /><?php _e('or in php code', 'brpv'); ?>:<br />
										<strong>do_shortcode('[pageviews]');</strong></p>
										<?php _e('Notice', 'brpv'); ?>:<br />
										- <?php _e('This shortcodes can be used in the body of the article', 'brpv'); ?>.<br />
										- <?php _e('This shortcodes can be used in the loop body of templates', 'brpv'); ?>.<br />
										- <?php _e('This shortcodes cannot be used outside the loop of the template', 'brpv'); ?>.
									</div>
								</div>
								<?php do_action('brpv_after_optional_elemet_block'); ?>
								<div class="postbox">
									<div class="inside">
										<table class="form-table"><tbody>
											<tr>
												<th scope="row"><label for="button-primary"></label></th>
												<td class="overalldesc"><?php wp_nonce_field('brpv_nonce_action', 'brpv_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="brpv_submit_action" value="<?php _e('Save', 'brpv'); ?>"/><br />
												<span class="description"><small><?php _e('Click to save the settings', 'brpv'); ?><small></span></td>
											</tr>
										</tbody></table>
									</div>
								</div>
							</form>
						</div>
					</div><!-- /postbox-container-2 -->

				</div>
			</div><!-- /poststuff -->
			<?php $this->get_html_icp_banners(); ?>
			<?php $this->get_html_my_plugins_list(); ?>
		</div><?php // end get_html_form();
	}

	public function get_html_tabs($current = 'main_tab') {
		$tabs = array(
			'main_tab' 		=> __('Main settings', 'brpv')
		);
		
		$html = '<div class="nav-tab-wrapper" style="margin-bottom: 10px;">';
			foreach ($tabs as $tab => $name) {
				if ($tab === $current) {
					$class = ' nav-tab-active';
				} else {
					$class = ''; 
				}
				if (isset($_GET['feed_id'])) {
					$nf = '&feed_id='.sanitize_text_field($_GET['feed_id']);
				} else {
					$nf = '';
				}
				$html .= sprintf('<a class="nav-tab%1$s" href="?page=brpvexport&tab=%2$s%3$s">%4$s</a>',$class, $tab, $nf, $name);
			}
		$html .= '</div>';

		return $html;
	} // end get_html_tabs();

	public function get_html_main_settings() { 	
		if (is_multisite()) {
			$not_count_bots = get_blog_option(get_current_blog_id(), 'brpv_not_count_bots');
			$brpv_rating_icons = get_blog_option(get_current_blog_id(), 'brpv_rating_icons');
		} else {
			$not_count_bots = get_option('brpv_not_count_bots');
			$brpv_rating_icons = get_option('brpv_rating_icons');
		}	
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Main settings', 'brpv'); ?></h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="brpv_tr">		
						<th scope="row"><label for="brpv_not_count_bots"><?php _e('Not count bots', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_not_count_bots">					
								<option value="yes" <?php selected($not_count_bots, 'yes'); ?>><?php _e('Yes', 'brpv'); ?></option>
								<option value="no" <?php selected($not_count_bots, 'no'); ?>><?php _e('No', 'brpv'); ?></option>
							</select><br />
							<span class="description"><?php _e('Do not count the bots visiting the site', 'brpv'); ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_rating_icons"><?php _e('Rating icons', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input class="brpv_radio" type="radio" name="brpv_rating_icons" value="brpv_pic1" <?php checked($brpv_rating_icons, 'brpv_pic1'); ?>> <img src="<?php echo BRPV_PLUGIN_DIR_URL.'img/ratings1.png'; ?>" alt="" /><br />
							<input class="brpv_radio" type="radio" name="brpv_rating_icons" value="brpv_pic2" <?php checked($brpv_rating_icons, 'brpv_pic2'); ?>> <img src="<?php echo BRPV_PLUGIN_DIR_URL.'img/ratings2.png'; ?>" alt="" /><br />
							<input class="brpv_radio" type="radio" name="brpv_rating_icons" value="brpv_pic3" <?php checked($brpv_rating_icons, 'brpv_pic3'); ?>> <img src="<?php echo BRPV_PLUGIN_DIR_URL.'img/ratings3.png'; ?>" alt="" /><br />
							<span class="description"><?php _e('Rating icons', 'brpv'); ?></span>
						</td>
					</tr>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_main_settings();

	public function get_html_icp_banners() { ?>
		<div id="icp_slides" class="clear">
			<div class="icp_wrap">
				<input type="radio" name="icp_slides" id="icp_point1">
				<input type="radio" name="icp_slides" id="icp_point2">
				<input type="radio" name="icp_slides" id="icp_point3">
				<input type="radio" name="icp_slides" id="icp_point4">
				<input type="radio" name="icp_slides" id="icp_point5" checked>
				<input type="radio" name="icp_slides" id="icp_point6">
				<input type="radio" name="icp_slides" id="icp_point7">
				<div class="icp_slider">
					<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
					<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
					<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
					<div class="icp_slides icp_img4"><a href="//wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"></a></div>
					<div class="icp_slides icp_img5"><a href="//wordpress.org/plugins/xml-for-avito/" target="_blank"></a></div>
					<div class="icp_slides icp_img6"><a href="//wordpress.org/plugins/xml-for-o-yandex/" target="_blank"></a></div>
					<div class="icp_slides icp_img7"><a href="//wordpress.org/plugins/import-from-yml/" target="_blank"></a></div>
				</div>
				<div class="icp_control">
					<label for="icp_point1"></label>
					<label for="icp_point2"></label>
					<label for="icp_point3"></label>
					<label for="icp_point4"></label>
					<label for="icp_point5"></label>
					<label for="icp_point6"></label>
					<label for="icp_point7"></label>
				</div>
			</div> 
		</div><?php 
	} // end get_html_icp_banners()

	public function get_html_my_plugins_list() { ?>
		<div class="metabox-holder">
			<div class="postbox">
				<h2 class="hndle"><?php _e('My plugins that may interest you', 'brpv'); ?></h2>
				<div class="inside">
					<p><span class="brpv_bold">XML for Google Merchant Center</span> - <?php _e('Сreates a XML-feed to upload to Google Merchant Center', 'brpv'); ?>. <a href="https://wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p> 
					<p><span class="brpv_bold">YML for Yandex Market</span> - <?php _e('Сreates a YML-feed for importing your products to Yandex Market', 'brpv'); ?>. <a href="https://wordpress.org/plugins/yml-for-yandex-market/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
					<p><span class="brpv_bold">Import from YML</span> - <?php _e('Imports products from YML to your shop', 'brpv'); ?>. <a href="https://wordpress.org/plugins/import-from-yml/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
					<p><span class="brpv_bold">XML for Hotline</span> - <?php _e('Сreates a XML-feed for importing your products to Hotline', 'brpv'); ?>. <a href="https://wordpress.org/plugins/xml-for-hotline/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
					<p><span class="brpv_bold">Gift upon purchase for WooCommerce</span> - <?php _e('This plugin will add a marketing tool that will allow you to give gifts to the buyer upon purchase', 'brpv'); ?>. <a href="https://wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
					<p><span class="brpv_bold">Import products to ok.ru</span> - <?php _e('With this plugin, you can import products to your group on ok.ru', 'brpv'); ?>. <a href="https://wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
					<p><span class="brpv_bold">XML for Avito</span> - <?php _e('Сreates a XML-feed for importing your products to', 'brpv'); ?> Avito. <a href="https://wordpress.org/plugins/xml-for-avito/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
					<p><span class="brpv_bold">XML for O.Yandex (Яндекс Объявления)</span> - <?php _e('Сreates a XML-feed for importing your products to', 'brpv'); ?> Яндекс.Объявления. <a href="https://wordpress.org/plugins/xml-for-o-yandex/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
				</div>
			</div>
		</div><?php
	} // end get_html_my_plugins_list()

	public function admin_head_css_func() {
		/* печатаем css в шапке админки */
		print '<style>/* Best Rating & Pageviews */
			.metabox-holder .postbox-container .empty-container {height: auto !important;}
			.icp_img1 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl1.jpg);}
			.icp_img2 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl2.jpg);}
			.icp_img3 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl3.jpg);}
			.icp_img4 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl4.jpg);}
			.icp_img5 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl5.jpg);}
			.icp_img6 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl6.jpg);}
			.icp_img7 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl7.jpg);}
		</style>';
	}

	private function init_hooks() {
		// наш класс, вероятно, вызывается во время срабатывания хука admin_menu.
		// admin_init - следующий в очереди срабатывания, хуки раньше admin_menu нет смысла вешать
		// add_action('admin_init', array($this, 'listen_submits'), 10);
		add_action('admin_print_footer_scripts', array($this, 'admin_head_css_func'));
	}

	private function listen_submit() { /*
		$def_plugin_date_arr = new BRPV_Data_Arr();
		$opts_name_and_def_date_arr = $def_plugin_date_arr->get_opts_name_and_def_date('public');
		foreach ($opts_name_and_def_date_arr as $key => $value) {
			$save_if_empty = false;
			switch ($key) {
				case 'brpv_status_cron': 
						case 'brpv_status_cron': 
				case 'brpv_status_cron': 
					if (!isset($_GET['tab']) || ($_GET['tab'] !== 'filtration')) {
						continue 2;
					} else {
						$save_if_empty = true;
					}
					break;
			}
			$this->save_plugin_set($key, $feed_id, $save_if_empty);
		} */
		return;
	}

	private function save_plugin_set($opt_name, $feed_id, $save_if_empty = false) {
		if (isset($_POST[$opt_name])) {
			brpv_optionUPD($opt_name, sanitize_text_field($_POST[$opt_name]), $feed_id, 'yes', 'set_arr');
		} else {
			if ($save_if_empty === true) {
				brpv_optionUPD($opt_name, '0', $feed_id, 'yes', 'set_arr');
			}
		}
		return;
	}
}