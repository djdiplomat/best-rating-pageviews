<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Statistics Page
*
* @link			https://icopydoc.ru/
* @since		2.1.0
*/

class BRPV_Statistics_Page {
	private $feed_id;
	private $feedback;

	public function __construct() {
		$this->init_hooks(); // подключим хуки
		$this->listen_submit();

		$this->get_html_form();	
	}

	public function get_html_form() { ?>
	
		<?php // end get_html_form();
	}

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

	private function listen_submit() {
		return;
	}
}