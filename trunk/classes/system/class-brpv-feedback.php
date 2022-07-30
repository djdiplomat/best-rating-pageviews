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
				<p><?php _e('Please help make the plugin better', 'brpv'); ?> <a href="https://docs.google.com/forms/d/1Nv25ESNvJtUA0i3MQRmFy9pvK1qgEUgq-IJzIcGfvp4" target="_blank" ><?php _e('answering 3 questions', 'brpv'); ?></a>!</p>
				<p><?php _e('If this plugin useful to you, please support the project one way', 'brpv'); ?>:</p>
				<ul class="brpv_ul">
					<li><a href="//wordpress.org/support/plugin/best-rating-pageviews/reviews/" target="_blank"><?php _e('Leave a comment on the plugin page', 'brpv'); ?></a>.</li>
					<li><?php _e('Support the project financially', 'brpv'); ?>. <a href="//sobe.ru/na/best_rating_pageviews" target="_blank"> <?php _e('Donate now', 'brpv'); ?></a>.</li>
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
			$unixtime = current_time('Y-m-d H:i');
			$mail_content = '<h1>Заявка (#'.$unixtime.')</h1>';
			$mail_content .= "Версия плагина: ". brpv_PLUGIN_VERSION . "<br />";
			$mail_content .= "Версия WP: ".get_bloginfo('version'). "<br />";
			$mail_content .= "Версия PHP: ".phpversion(). "<br />";   
			$mail_content .= "Режим мультисайта: ".$brpv_is_multisite. "<br />";
			if (isset($_POST[$this->get_radio_name()])) {
				$mail_content .= PHP_EOL ."Помог ли плагин: ".sanitize_text_field($_POST[$this->get_radio_name()]);
			} 
			if (isset($_POST[$this->get_input_name()])) {				
				$mail_content .= '<br />Почта: <a href="mailto:'.sanitize_email($_POST[$this->get_input_name()]).'?subject=Best Rating & Pageviews (#'.$unixtime.')" target="_blank" rel="nofollow noreferer" title="'.sanitize_email($_POST['yfym_email']).'">'.sanitize_email($_POST['yfym_email']).'</a>';
			}
			if (isset($_POST[$this->get_textarea_name()])) {
				$mail_content .= "<br />Сообщение: ".sanitize_text_field($_POST[$this->get_textarea_name()]);
			}
			add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
			wp_mail('support@icopydoc.ru', 'Отчёт Best Rating & Pageviews', $mail_content);
			// Сбросим content-type, чтобы избежать возможного конфликта
			remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
		}
	}

	public static function set_html_content_type() {
		return 'text/html';
	}
}
?>