<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Name: Best Rating & Pageviews
* Plugin URI: https://icopydoc.ru/category/documentation/
* Description: Add Star rating, pageviews and adds a tool for analyzing the effectiveness of content. Also this plugin adds a widget which shows popular posts and pages based on the rating and pageviews.
* Version: 2.1.0
* Requires at least: 4.5
* Requires PHP: 5.6
* Author: Maxim Glazunov
* Author URI: https://icopydoc.ru
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: brpv
* Domain Path: /languages
* Tags: rating, stars, pageviews, widget, popular
*
* This program is free software; you can redistribute it and/or modify it under the terms of the GNU
* General Public License version 2, as published by the Free Software Foundation. You may NOT assume
* that you can use any other version of the GPL.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* 
* Copyright 2018-2022 (Author emails: djdiplomat@yandex.ru, support@icopydoc.ru)
*/ 
$upload_dir = wp_get_upload_dir();
define('BRPV_SITE_UPLOADS_URL', $upload_dir['baseurl']); // http://site.ru/wp-content/uploads
define('BRPV_SITE_UPLOADS_DIR_PATH', $upload_dir['basedir']); // /home/site.ru/public_html/wp-content/uploads

define('BRPV_PLUGIN_VERSION', '2.1.0'); // 1.0.0
define('BRPV_PLUGIN_UPLOADS_DIR_URL', $upload_dir['baseurl'].'/best-rating-pageviews'); // http://site.ru/wp-content/uploads/best-rating-pageviews
define('BRPV_PLUGIN_UPLOADS_DIR_PATH', $upload_dir['basedir'].'/best-rating-pageviews'); // /home/site.ru/public_html/wp-content/uploads/best-rating-pageviews
define('BRPV_PLUGIN_DIR_URL', plugin_dir_url(__FILE__)); // http://site.ru/wp-content/plugins/best-rating-pageviews/
define('BRPV_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__)); // /home/p135/www/site.ru/wp-content/plugins/best-rating-pageviews/
define('BRPV_PLUGIN_MAIN_FILE_PATH', __FILE__); // /home/p135/www/site.ru/wp-content/plugins/best-rating-pageviews/best-rating-pageviews.php
define('BRPV_PLUGIN_SLUG', wp_basename(dirname(__FILE__))); // best-rating-pageviews - псевдоним плагина
define('BRPV_PLUGIN_BASENAME', plugin_basename(__FILE__)); // best-rating-pageviews/best-rating-pageviews.php - полный псевдоним плагина (папка плагина + имя главного файла)
unset($upload_dir);

require_once plugin_dir_path(__FILE__).'/packages.php';
register_activation_hook(__FILE__, array('BestRatingPageviews', 'on_activation'));
register_deactivation_hook(__FILE__, array('BestRatingPageviews', 'on_deactivation'));
add_action('plugins_loaded', array('BestRatingPageviews', 'init'));

final class BestRatingPageviews {
	private $site_uploads_url = BRPV_SITE_UPLOADS_URL; // http://site.ru/wp-content/uploads
	private $site_uploads_dir_path = BRPV_SITE_UPLOADS_DIR_PATH; // /home/site.ru/public_html/wp-content/uploads
	private $plugin_version = BRPV_PLUGIN_VERSION; // 1.0.0
	private $plugin_upload_dir_url = BRPV_PLUGIN_UPLOADS_DIR_URL; // http://site.ru/wp-content/uploads/brpv/
	private $plugin_upload_dir_path = BRPV_PLUGIN_UPLOADS_DIR_PATH; // /home/site.ru/public_html/wp-content/uploads/brpv/
	private $plugin_dir_url = BRPV_PLUGIN_DIR_URL; // http://site.ru/wp-content/plugins/best-rating-pageviews/
	private $plugin_dir_path = BRPV_PLUGIN_DIR_PATH; // /home/p135/www/site.ru/wp-content/plugins/best-rating-pageviews/
	private $plugin_main_file_path = BRPV_PLUGIN_MAIN_FILE_PATH; // /home/p135/www/site.ru/wp-content/plugins/best-rating-pageviews/best-rating-pageviews.php
	private $plugin_slug = BRPV_PLUGIN_SLUG; // best-rating-pageviews - псевдоним плагина
	private $plugin_basename = BRPV_PLUGIN_BASENAME; // best-rating-pageviews/best-rating-pageviews.php - полный псевдоним плагина (папка плагина + имя главного файла)

	protected static $instance;
	public static function init() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;  
	}

	// Срабатывает при активации плагина (вызывается единожды)
	public static function on_activation() {
		if (!current_user_can('activate_plugins')) {return;}
		if (is_multisite()) {
			add_blog_option(get_current_blog_id(), 'brpv_version', '2.1.0');
			add_blog_option(get_current_blog_id(), 'brpv_not_count_bots', 'yes');
			add_blog_option(get_current_blog_id(), 'brpv_rating_icons', 'brpv_pic1');
		} else {
			add_option('brpv_version', '2.1.0', '', 'no');
			add_option('brpv_not_count_bots', 'yes'); // Учитывать ботов?
			add_option('brpv_rating_icons', 'brpv_pic1');
		}
	}

	// Срабатывает при отключении плагина (вызывается единожды)
	public static function on_deactivation() {
		if (!current_user_can('activate_plugins')) {return;}	
	}

	public function __construct() {
		load_plugin_textdomain('brpv', false, $this->plugin_slug.'/languages/'); // load translation
		$this->check_options_upd(); // проверим, нужны ли обновления опций плагина
		$this->init_classes();
		$this->init_hooks(); // подключим хуки
	}

	public function check_options_upd() {
		$plugin_version = $this->get_plugin_version();
		if ($plugin_version == false) { // вероятно, у нас первичная установка плагина
			if (is_multisite()) {
				update_blog_option(get_current_blog_id(), 'brpv_version', BRPV_PLUGIN_VERSION);
			} else {
				update_option('brpv_version', BRPV_PLUGIN_VERSION);
			}
		} else if ($plugin_version !== $this->plugin_version) {
			add_action('init', array($this, 'set_new_options'), 10); // автообновим настройки, если нужно
		}
	}

	public function get_plugin_version() {
		if (is_multisite()) {
			$v = get_blog_option(get_current_blog_id(), 'brpv_version');
		} else {
			$v = get_option('brpv_version');
		}
		return $v;
	}

	public function set_new_options() {
		// удаление старых опций
		if (brpv_optionGET('brpv_debug') !== false) {brpv_optionDEL('brpv_debug');}

		// добавление новых опций
		// if (brpv_optionGET('brpv_version') === false) {brpv_optionUPD('2.1.0', '', '', 'no');}

		if (is_multisite()) {
			update_blog_option(get_current_blog_id(), 'brpv_version', BRPV_PLUGIN_VERSION);
		} else {
			update_option('brpv_version', BRPV_PLUGIN_VERSION);
		}
		return;
	}

	public function init_classes() {
		return;
	}

	public function init_hooks() {		
		add_action('admin_init', array($this, 'listen_submits_func'), 10); // ещё можно слушать чуть раньше на wp_loaded
		add_action('admin_menu', array($this, 'add_admin_menu_func'));

		add_action('wp_head',  array($this, 'session_counter')); // cчетчик посещений
		add_action('wp_enqueue_scripts', array($this, 'brpv_enqueue_fp'));
		add_action('wp_enqueue_scripts', array($this, 'register_style_frontend'));
		add_action('admin_notices', array($this, 'print_admin_notices_func'));
		add_action('wp_ajax_brpv_ajax_func',  array($this, 'brpv_ajax_func'));
		add_action('wp_ajax_nopriv_brpv_ajax_func',  array($this, 'brpv_ajax_func'));
		
		add_shortcode('pageviews', array($this, 'brpv_pageviews_func'));
		add_shortcode('pageratings', array($this, 'brpv_pageratings_func')); /* шорткод рейтинг поста */
		
		/* Регаем стили только для страницы настроек плагина */
		add_action('admin_init', function() {
			wp_register_style('brpv-admin-css', plugins_url('css/brpv.css', __FILE__));
		}, 9999);	

		add_filter('plugin_action_links', array($this, 'add_plugin_action_links'), 10, 2 );
	}

	public function listen_submits_func() {
		do_action('brpv_listen_submits');

		if (isset($_REQUEST['brpv_submit_action'])) {
			if (!empty($_POST) && check_admin_referer('brpv_nonce_action', 'brpv_nonce_field')) {
				if (is_multisite()) {
					if (isset($_POST['brpv_submit_action'])) {
						update_blog_option(get_current_blog_id(), 'brpv_not_count_bots', sanitize_text_field($_POST['brpv_not_count_bots']));
					}
					if (isset($_POST['brpv_rating_icons'])) {
						update_blog_option(get_current_blog_id(), 'brpv_rating_icons', sanitize_text_field($_POST['brpv_rating_icons']));
					}
				} else {
					if (isset($_POST['brpv_submit_action'])) {
						update_option('brpv_not_count_bots', sanitize_text_field($_POST['brpv_not_count_bots']));
					}
					if (isset($_POST['brpv_rating_icons'])) {
						update_option('brpv_rating_icons', sanitize_text_field($_POST['brpv_rating_icons']));
					}
				}
			}

			$message = __('Updated', 'brpv');
			$class = 'notice-success';
			add_action('admin_notices', function() use ($message, $class) { 
				$this->admin_notices_func($message, $class);
			}, 10, 2);
		}

		if (isset($_REQUEST['brpv_submit_clear_stat'])) {
			if (!empty($_POST) && check_admin_referer('brpv_nonce_action_clear_stat', 'brpv_nonce_clear_stat_field')) {
				$args = array(
					'post_type' => array('post', 'page', 'product'),
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'relation' => 'AND',
					'fields'  => 'ids',
					'meta_query' => array(
						array(
							'key' => 'brpv_pageviews',
							'compare' => 'EXISTS'
						)
					)
				);
				$res_query = new WP_Query($args);
				global $wpdb;
				if ($res_query->have_posts()) { 
					for ($i = 0; $i < count($res_query->posts); $i++) {
						delete_post_meta($res_query->posts[$i], 'brpv_ballov');
						delete_post_meta($res_query->posts[$i], 'brpv_golosov');
						delete_post_meta($res_query->posts[$i], 'brpv_lastime');
						delete_post_meta($res_query->posts[$i], 'brpv_pageviews');
						delete_post_meta($res_query->posts[$i], 'brpv_total_rating');
					}
				}

				$message = __('Statistics deleted', 'brpv');
				$class = 'notice-success';
				add_action('admin_notices', function() use ($message, $class) { 
					$this->admin_notices_func($message, $class);
				}, 10, 2);
			}
		}
	}

	// Добавляем пункты меню
	public function add_admin_menu_func() {
		$page_suffix = add_menu_page(null , __('Statistics', 'brpv'), 'unfiltered_html', 'brpv-statistics', array($this, 'get_statistics_page_func'), 'dashicons-chart-bar', 51);	
		add_action('admin_print_styles-'. $page_suffix, array($this, 'enqueue_style_admin_css_func')); // создаём хук, чтобы стили выводились только на странице настроек

		$page_suffix = add_submenu_page('brpv-statistics', __('Settings', 'brpv'), __('Settings', 'brpv'), 'unfiltered_html', 'brpv-settings', array($this, 'get_settings_page_func'));
		add_action('admin_print_styles-'. $page_suffix, array($this, 'enqueue_style_admin_css_func'));

		// $page_subsuffix = add_submenu_page('brpvexport', __('Add Extensions', 'brpv'), __('Extensions', 'brpv'), 'manage_woocommerce', 'brpv-extensions', 'brpv_extensions_page');
		// require_once BRPV_PLUGIN_DIR_PATH.'/extensions.php';
		// add_action('admin_print_styles-'. $page_subsuffix, array($this, 'enqueue_style_admin_css_func'));
	} 

	// вывод страницы настроек плагина
	public function get_settings_page_func() {
		new BRPV_Settings_Page();
		return;
	} 

	// вывод страницы настроек плагина
	public function get_statistics_page_func() {
		new BRPV_Statistics_Page();
		return;
	} 

	public function enqueue_style_admin_css_func() {
		wp_enqueue_style('brpv-admin-css'); /* Ставим css-файл в очередь на вывод */
	} 
	
	public function session_counter() {
		// https://habrahabr.ru/sandbox/74080/
		if (is_singular()) { // Функция объединяет в себе : is_single(), is_page(), is_attachment() и и произвольные типы записей.
			// если не учитываем ботов
			if (is_multisite()) {
				$not_count_bots = get_blog_option(get_current_blog_id(), 'brpv_not_count_bots');
			} else {
				$not_count_bots = get_option('brpv_not_count_bots');
			}
			if ($not_count_bots == 'yes') {
				if (strstr($_SERVER['HTTP_USER_AGENT'], 'YandexBot')) {$bot='YandexBot';} //Выявляем поисковых ботов		
				$useragent = $_SERVER['HTTP_USER_AGENT']; 
				$bot = "Bot\|robot\|Slurp\|yahoo|\YandexBot|\Googlebot";
				$notbot = "Mozilla\|Opera"; /* Браузеры кроме Opera представляются как Mozilla
				*	Напримерм, если к нам на сайт зашел человек, а не бот, то $useragent будет таким:
				*	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/51.0.2704.101 Safari/537.31 
				*/
				if (!preg_match("|$notbot|U", $useragent) || preg_match("|$bot|U", $useragent)) {
					$this->add_pageviews(); 
				}
			} else {
				// если ботов пропускать не нужно - добавляем счетчик
				$this->add_pageviews();
			}
		}
		return true;
	}
 
	public function brpv_pageviews_func() {
		global $post;
		if (get_post_meta($post->ID, 'brpv_pageviews', true)) {
			echo get_post_meta($post->ID, 'brpv_pageviews', true); /*.$_SERVER['HTTP_USER_AGENT'];*/
		} else {
			echo "0";
		}
	}	

	function brpv_pageratings_func() {
		global $post;
		$post_id = (int)$post->ID; 
		if (get_post_meta($post_id, 'brpv_total_rating', true)) {
			$ratingValue = get_post_meta($post_id, 'brpv_total_rating', true);
		} else {
			$ratingValue = 0;
		}	
		/* число голосов */
		if (get_post_meta($post_id, 'brpv_golosov', true)) {
			$ratingCount = (int)get_post_meta($post_id, 'brpv_golosov', true);
		} else {
			$ratingCount = 0;
		}
		if (is_multisite()) {
			$rating_icons = get_blog_option(get_current_blog_id(), 'brpv_rating_icons');
		} else {
			$rating_icons = get_option('brpv_rating_icons');
		}
		$itemReviewed = esc_html($post->post_title);
		?>
		<div style="display: none;" itemprop="aggregateRating" itemscope="" itemtype="https://schema.org/AggregateRating"><meta itemprop="bestRating" content="5"><meta itemprop="ratingValue" content="<?php echo $ratingValue; ?>"><meta itemprop="ratingCount" content="<?php echo $ratingCount; ?>"><meta itemprop="itemReviewed" content="<?php echo $itemReviewed; ?>"></div>	
		<div class="brpv_raiting_star_<?php echo $post_id; ?>">
			<div class="raiting">
				<div class="raiting_blank <?php echo $rating_icons; ?>"></div>
				<div class="raiting_hover <?php echo $rating_icons; ?>"></div>
				<div class="raiting_votes <?php echo $rating_icons; ?>"></div>
			</div>
			<div class="raiting_info"><img src="<?php echo BRPV_PLUGIN_DIR_URL.'img/'; ?>load.gif" /><strong><?php _e('Raiting', 'brpv'); ?>:</strong> <span class="brpv_raiting_value"></span></div>
			<div style="display: none;" class="hidden" postid="<?php echo $post_id; ?>" ratingvalue="<?php echo $ratingValue; ?>"></div>
		</div><?php 
	}
	/* end шорткод рейтинг поста */ 
 
	/* Функция Аякс обработчика рейтинга */
	public function brpv_ajax_func(){		
		$result = array();
		if (isset($_REQUEST['user_votes']) && isset($_REQUEST['postId'])) {
			$user_votes = (int)sanitize_text_field($_REQUEST['user_votes']); // получаем оценку, которую поставил пользователь	
			$post_id = (int)sanitize_text_field($_REQUEST['postId']); // id поста, которому поставили оценку
		} else {$result['status'] = "false"; $result = json_encode($result); echo $result; die();} 
		
		if (get_post_meta($post_id, 'brpv_golosov', true)) { 
			$golosov = (int)get_post_meta($post_id, 'brpv_golosov', true); 
		} else {$golosov = (int)0;}
		if (get_post_meta($post_id, 'brpv_ballov', true)) { 
			$ballov = (int)get_post_meta($post_id, 'brpv_ballov', true); 
		} else {$ballov = (int)0;}
		if (get_post_meta($post_id, 'brpv_total_rating', true)) {
			$total_rating = (int)get_post_meta($post_id, 'brpv_total_rating', true);
		} else {$total_rating = (int)0;}
		
		$golosov_new = $golosov + 1; // нове значение проголосовавших
		$ballov_new = $ballov + $user_votes; // нове значение баллов
		$total_rating_new = $ballov_new / $golosov_new; // общая оценка
		$total_rating_new = round($total_rating_new, 2); // округляем до сотых
		
		update_post_meta($post_id, 'brpv_golosov', $golosov_new);
		update_post_meta($post_id, 'brpv_ballov', $ballov_new);
		update_post_meta($post_id, 'brpv_total_rating', $total_rating_new);

		$result['user_votes'] = $user_votes;
		$result['postId'] = $post_id;
		$result['golosov_old'] = $golosov;
		$result['golosov_new'] = $golosov_new;
		$result['ballov_old'] = $ballov;
		$result['ballov_new'] = $ballov_new;
		$result['total_rating_new'] = $total_rating_new;
		$result['total_rating_old'] = $total_rating;
		$result['status'] = "success";
		
		$result = json_encode($result);
		echo $result;
		die();
	}
	/* end Функция Аякс обработчика рейтинга */ 
 
	/* Подключение таблицы стилей только для фронтенда */
	public function register_style_frontend() {	 
		wp_register_style('brpv_style', BRPV_PLUGIN_DIR_URL . 'css/rating.css', '', null, 'all' );
		wp_enqueue_style('brpv_style', '', '', '', true); // подключаем в футре
	}
	
	// регистрируем скрипты для внешней части сайта
	public function brpv_enqueue_fp() { 
		wp_register_script('brpv_rating', BRPV_PLUGIN_DIR_URL . 'js/rating.js');
		wp_enqueue_script('brpv_rating', '', '', array('jquery'), true); // подключаем в футре
		wp_register_script('brpv_jquery_cookiess', BRPV_PLUGIN_DIR_URL . 'js/jquery.cookies.js');
		wp_enqueue_script('brpv_jquery_cookiess', '', '', array('jquery'), true);
		wp_localize_script('brpv_rating', 'brpvajax', array('brpvajaxurl' => admin_url('admin-ajax.php')));	
	} 
 
	public function print_admin_notices_func() {
		return;
	}

	public function add_plugin_action_links($actions, $plugin_file) {
		if (false === strpos($plugin_file, basename(__FILE__))) { // проверка, что у нас текущий плагин
			return $actions;
		}
		$settings_link = '<a href="/wp-admin/admin.php?page=brpvsettings">'. __('Settings', 'brpv').'</a>';
		array_unshift($actions, $settings_link);
		return $actions;
	}

	/* Функция увеличения счетчика просмотров */
	private function add_pageviews() {
		global $user_ID, $post;
		$post_id = (int)$post->ID; // получаем id поста
		$lastime = current_time('timestamp');
		$pageviews = (int)get_post_meta($post_id, 'brpv_pageviews', true); // получаем число постов
		update_post_meta($post_id, 'brpv_pageviews', ($pageviews + 1));
		update_post_meta($post_id, 'brpv_lastime', $lastime);
	}

	private function admin_notices_func($message, $class) {
		printf('<div class="notice %1$s"><p>%2$s</p></div>', $class, $message);
		return;
	}
} /* end class BestRatingPageviews */
?>