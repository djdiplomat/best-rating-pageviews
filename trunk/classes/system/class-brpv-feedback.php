<?php if (!defined('ABSPATH')) {exit;}
/**
* Sends feedback about the plugin
*
* @link			https://icopydoc.ru/
* @since		2.1.0
*/

final class BRPV_Feedback {
	private $pref = 'brpv';	

	public function __construct($pref = null) {
		if ($pref) {$this->pref = $pref;}

		$this->listen_submits_func();
	}

	public function get_form() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Send data about the work of the plugin', 'brpv'); ?></h2>
			<div class="inside">
				<p><?php _e('Sending statistics you help make the plugin even better', 'brpv'); ?>! <?php _e('The following data will be transferred', 'brpv'); ?>:</p>
				<ul class="brpv_ul">
					<li><?php _e('URL your feeds', 'brpv'); ?></li>
					<li><?php _e('Files generation status', 'brpv'); ?></li>
					<li><?php _e('PHP version information ', 'brpv'); ?></li>
					<li><?php _e('Multisite mode status', 'brpv'); ?></li>
					<li><?php _e('Technical information and plugin logs', 'brpv'); ?> Best Rating & Pageviews</li>
				</ul>
				<p><?php _e('Did my plugin help you upload your products to the', 'brpv'); ?> Best Rating & Pageviews?</p>
				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
					<p>
						<input type="radio" name="<?php echo $this->get_radio_name(); ?>" value="yes"><?php _e('Yes', 'brpv'); ?><br />
						<input type="radio" name="<?php echo $this->get_radio_name(); ?>" value="no"><?php _e('No', 'brpv'); ?><br />
					</p>
					<p><?php _e("If you don't mind to be contacted in case of problems, please enter your email address", "brpv"); ?>.</p>
					<p><input type="email" name="<?php echo $this->get_input_name(); ?>"></p>
					<p><?php _e('Your message', 'brpv'); ?>:</p>
					<p><textarea rows="6" cols="32" name="<?php echo $this->get_textarea_name(); ?>" placeholder="<?php _e('Enter your text to send me a message (You can write me in Russian or English). I check my email several times a day', 'brpv'); ?>"></textarea></p>
					<?php wp_nonce_field($this->get_nonce_action(), $this->get_nonce_field()); ?>
					<input class="button-primary" type="submit" name="<?php echo $this->get_submit_name(); ?>" value="<?php _e('Send data', 'brpv'); ?>" />
				</form>	
			</div>
		</div><?php
	}

	public function get_block_support_project() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Please support the project', 'brpv'); ?>!</h2>
			<div class="inside">	  
				<p><?php _e('Thank you for using the plugin', 'brpv'); ?> <strong>Best Rating & Pageviews</strong></p>
				<p><?php _e('Please help make the plugin better', 'brpv'); ?> <a href="https://docs.google.com/forms/d/e/1FAIpQLSdmEXYIQzW-_Hj2mwvVbzKT8UUKaScJWQjDwcgI7Y5D0Xmchw/viewform" target="_blank" ><?php _e('answering 6 questions', 'brpv'); ?>!</a></p>
				<p><?php _e('If this plugin useful to you, please support the project one way', 'brpv'); ?>:</p>
				<ul class="brpv_ul">
					<li><a href="//wordpress.org/support/plugin/yml-for-yandex-market/reviews/" target="_blank"><?php _e('Leave a comment on the plugin page', 'brpv'); ?></a>.</li>
					<li><?php _e('Support the project financially', 'brpv'); ?>. <a href="//sobe.ru/na/plugin_yml_for_yandex_market" target="_blank"> <?php _e('Donate now', 'brpv'); ?></a>.</li>
					<li><?php _e('Noticed a bug or have an idea how to improve the quality of the plugin', 'brpv'); ?>? <a href="mailto:support@icopydoc.ru"><?php _e('Let me know', 'brpv'); ?></a>.</li>
				</ul>
				<p><?php _e('The author of the plugin Maxim Glazunov', 'brpv'); ?>.</p>
				<p><span style="color: red;"><?php _e('Accept orders for individual revision of the plugin', 'brpv'); ?></span>:<br /><a href="mailto:support@icopydoc.ru"><?php _e('Leave a request', 'brpv'); ?></a>.</p>
			</div>
		</div><?php
	}
	
	private function get_pref() {
		return $this->pref;
	}

	private function get_radio_name() {
		return $this->get_pref().'_its_ok';
	}

	private function get_input_name() {
		return $this->get_pref().'_email';
	}

	private function get_textarea_name() {
		return $this->get_pref().'_message';
	}

	private function get_submit_name() {
		return $this->get_pref().'_submit_send_stat';
	}

	private function get_nonce_action() {
		return $this->get_pref().'_nonce_action_send_stat';
	}

	private function get_nonce_field() {
		return $this->get_pref().'_nonce_field_send_stat';
	}

	private function listen_submits_func() {
		if (isset($_REQUEST[$this->get_submit_name()])) {
			$this->send_data();
			add_action('admin_notices', function() {
				$class = 'notice notice-success is-dismissible';
				$message = __('The data has been sent. Thank you', 'brpv');
				printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
			}, 9999);
		}
	}

	private function send_data() {
		if (!empty($_POST) && check_admin_referer($this->get_nonce_action(), $this->get_nonce_field())) { 	
			if (is_multisite()) { 
				$brpv_is_multisite = 'включен';	
				$brpv_keeplogs = get_blog_option(get_current_blog_id(), 'brpv_keeplogs');
			} else {
				$brpv_is_multisite = 'отключен'; 
				$brpv_keeplogs = get_option('brpv_keeplogs');
			}
			$feed_id = '1'; // (string)
			$unixtime = current_time('Y-m-d H:i');
			$mail_content = '<h1>Заявка (#'.$unixtime.')</h1>';
			$mail_content .= "Версия плагина: ". brpv_PLUGIN_VERSION . "<br />";
			$mail_content .= "Версия WP: ".get_bloginfo('version'). "<br />";	 
			$woo_version = brpv_get_woo_version_number();
			$mail_content .= "Версия WC: ".$woo_version. "<br />";
			$mail_content .= "Версия PHP: ".phpversion(). "<br />";   
			$mail_content .= "Режим мультисайта: ".$brpv_is_multisite. "<br />";
			$mail_content .= "Вести логи: ".$brpv_keeplogs. "<br />";
			$upload_dir = wp_get_upload_dir();
			$mail_content .= 'Расположение логов: <a href="'.$upload_dir['baseurl'].'/brpv/plugins.log" target="_blank">'.$upload_dir['basedir'].'/brpv/brpv.log</a><br />';	
			$possible_problems_arr = brpv_Debug_Page::get_possible_problems_list();
			if ($possible_problems_arr[1] > 0) {
				$possible_problems_arr[3] = str_replace('<br/>', PHP_EOL, $possible_problems_arr[3]);
				$mail_content .= "Самодиагностика: ". PHP_EOL .$possible_problems_arr[3];
			} else {
				$mail_content .= "Самодиагностика: Функции самодиагностики не выявили потенциальных проблем". "<br />";
			}
			if (!class_exists('YmlforYandexMarketAliexpress')) {
				$mail_content .= "Aliexpress Export: не активна". "<br />";
			} else {
				$order_id = brpv_optionGET('brpvae_order_id');
				$order_email = brpv_optionGET('brpvae_order_email');
				$mail_content .= "Aliexpress Export: активна (v ".brpvae_VER." (#".$order_id." / ".$order_email."))". "<br />";
			}
			if (!class_exists('YmlforYandexMarketBookExport')) {
				$mail_content .= "Book Export: не активна". "<br />";
			} else {
				if (!defined('brpvbe_VER')) {define('brpvbe_VER', 'н/д');} 
				$order_id = brpv_optionGET('brpvbe_order_id');
				$order_email = brpv_optionGET('brpvbe_order_email');
				$mail_content .= "Book Export: активна (v ".brpvbe_VER." (#".$order_id." / ".$order_email."))". "<br />";
			}
			if (!class_exists('YmlforYandexMarketPro')) {
				$mail_content .= "Pro: не активна". "<br />";
			} else {
				if (!defined('brpvp_VER')) {define('brpvp_VER', 'н/д');} 
				$order_id = brpv_optionGET('brpvp_order_id');
				$order_email = brpv_optionGET('brpvp_order_email');			
				$mail_content .= "Pro: активна (v ".brpvp_VER." (#".$order_id." / ".$order_email."))". "<br />";
			}
			if (!class_exists('YmlforYandexMarketProm')) {
				$mail_content .= "Prom Export: не активна". "<br />";
			} else {
				$order_id = brpv_optionGET('brpvpr_order_id');
				$order_email = brpv_optionGET('brpvpr_order_email');
				$mail_content .= "Prom Export: активна (v ".brpvpr_VER." (#".$order_id." / ".$order_email."))". "<br />";
			}		
			if (!class_exists('YmlforYandexMarketPromosExport')) {
				$mail_content .= "Promos Export: не активна". "<br />";
			} else {
				if (!defined('brpvpe_VER')) {define('brpvpe_VER', 'н/д');} 
				$order_id = brpv_optionGET('brpvpe_order_id');
				$order_email = brpv_optionGET('brpvpe_order_email');
				$mail_content .= "Promos Export: активна (v ".brpvpe_VER." (#".$order_id." / ".$order_email."))". "<br />";
			}
			if (!class_exists('YmlforYandexMarketRozetka')) {
				$mail_content .= "Prom Export: не активна". "<br />";
			} else {
				$order_id = brpv_optionGET('brpvre_order_id');
				$order_email = brpv_optionGET('brpvre_order_email');
				$mail_content .= "Rozetka Export: активна (v ".brpvre_VER." (#".$order_id." / ".$order_email."))". "<br />";
			}
			$yandex_zen_rss = brpv_optionGET('yzen_yandex_zen_rss');
			$mail_content .= "RSS for Yandex Zen: ".$yandex_zen_rss. "<br />";
			if (isset($_POST[$this->get_radio_name()])) {
				$mail_content .= PHP_EOL ."Помог ли плагин: ".sanitize_text_field($_POST[$this->get_radio_name()]);
			} 
			if (isset($_POST[$this->get_input_name()])) {				
				$mail_content .= '<br />Почта: <a href="mailto:'.sanitize_email($_POST[$this->get_input_name()]).'?subject=Ответ разработчика Best Rating & Pageviews (#'.$unixtime.')" target="_blank" rel="nofollow noreferer" title="'.sanitize_email($_POST['brpv_email']).'">'.sanitize_email($_POST['brpv_email']).'</a>';
			}
			if (isset($_POST[$this->get_textarea_name()])) {
				$mail_content .= "<br />Сообщение: ".sanitize_text_field($_POST[$this->get_textarea_name()]);
			}
			$argsp = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1 );
			$products = new WP_Query($argsp);
			$vsegotovarov = $products->found_posts;
			$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
			$brpv_settings_arr_keys_arr = array_keys($brpv_settings_arr);
			for ($i = 0; $i < count($brpv_settings_arr_keys_arr); $i++) {
				$feed_id = $brpv_settings_arr_keys_arr[$i];
				$status_sborki = (int)brpv_optionGET('brpv_status_sborki', $feed_id);
				$brpv_file_url = urldecode(brpv_optionGET('brpv_file_url', $feed_id, 'set_arr'));
				$brpv_file_file = urldecode(brpv_optionGET('brpv_file_file', $feed_id, 'set_arr'));
				$brpv_whot_export = brpv_optionGET('brpv_whot_export', $feed_id, 'set_arr');
				$brpv_yml_rules = brpv_optionGET('brpv_yml_rules', $feed_id, 'set_arr');
				$brpv_skip_missing_products = brpv_optionGET('brpv_skip_missing_products', $feed_id, 'set_arr');	
				$brpv_skip_backorders_products = brpv_optionGET('brpv_skip_backorders_products', $feed_id, 'set_arr');
				$brpv_status_cron = brpv_optionGET('brpv_status_cron', $feed_id, 'set_arr');
				$brpv_ufup = brpv_optionGET('brpv_ufup', $feed_id, 'set_arr');	
				$brpv_date_sborki = brpv_optionGET('brpv_date_sborki', $feed_id, 'set_arr');
				$brpv_main_product = brpv_optionGET('brpv_main_product', $feed_id, 'set_arr');
				$brpv_errors = brpv_optionGET('brpv_errors', $feed_id, 'set_arr');

				$mail_content .= "<br />ФИД №: ".$i. "<br />";
				$mail_content .= "status_sborki: ".$status_sborki. "<br />";
				$mail_content .= "УРЛ: ".get_site_url(). "<br />";
				$mail_content .= "УРЛ YML-фида: ".$brpv_file_url . "<br />";
				$mail_content .= "Временный файл: ".$brpv_file_file. "<br />";
				$mail_content .= "Что экспортировать: ".$brpv_whot_export. "<br />";
				$mail_content .= "Придерживаться правил: ".$brpv_yml_rules. "<br />";
				$mail_content .= "Исключать товары которых нет в наличии (кроме предзаказа): ".$brpv_skip_missing_products. "<br />";
				$mail_content .= "Исключать из фида товары для предзаказа: ".$brpv_skip_backorders_products. "<br />";
				$mail_content .= "Автоматическое создание файла: ".$brpv_status_cron. "<br />";
				$mail_content .= "Обновить фид при обновлении карточки товара: ".$brpv_ufup. "<br />";
				$mail_content .= "Дата последней сборки XML: ".$brpv_date_sborki. "<br />";
				$mail_content .= "Что продаёт: ".$brpv_main_product. "<br />";
				$mail_content .= "Ошибки: ".$brpv_errors. "<br />";
			}

			add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
			wp_mail('support@icopydoc.ru', 'Отчёт YML for WP', $mail_content);
			// Сбросим content-type, чтобы избежать возможного конфликта
			remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
		}
	}

	public static function set_html_content_type() {
		return 'text/html';
	}
}
?>