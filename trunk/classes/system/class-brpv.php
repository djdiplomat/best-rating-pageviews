<?php if (!defined('ABSPATH')) {exit;}
/**
 * The main class of the plugin Best Rating & Pageviews
 *
 * @package			Best Rating & Pageviews
 * @subpackage		
 * @since			0.1.0
 * 
 * @version			1.0.0 (20-03-2023)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				
 * 
 * @param		
 *
 * @return		
 *
 * @depends			classes:	BRPV_Settings_Page
 *					traits:	
 *					methods:	
 *					functions:	common_option_get
 *								common_option_upd
 *					constants:	BRPV_PLUGIN_VERSION
 *								BRPV_PLUGIN_BASENAME
 *								BRPV_PLUGIN_DIR_URL
 *					options:	
 *
 */

final class BestRatingPageviews {
	private $plugin_version = BRPV_PLUGIN_VERSION; // 1.0.0

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
			add_blog_option(get_current_blog_id(), 'brpv_version', '3.0.1');
			add_blog_option(get_current_blog_id(), 'brpv_posts_type_arr', array('post', 'page'));
			add_blog_option(get_current_blog_id(), 'brpv_not_count_bots', 'yes');
			add_blog_option(get_current_blog_id(), 'brpv_main_color', '#ffc000');
			add_blog_option(get_current_blog_id(), 'brpv_hover_color', '#ff5500');
		} else {
			add_option('brpv_version', '3.0.1', '', 'no');
			add_option('brpv_posts_type_arr', array('post', 'page'));
			add_option('brpv_not_count_bots', 'yes'); // Учитывать ботов?
			add_option('brpv_main_color', '#ffc000');
			add_option('brpv_hover_color', '#ff5500');
		}
	}

	// Срабатывает при отключении плагина (вызывается единожды)
	public static function on_deactivation() {
		if (!current_user_can('activate_plugins')) {return;}	
	}

	public function __construct() {
		$this->check_options_upd(); // проверим, нужны ли обновления опций плагина
		$this->init_classes();
		$this->init_hooks(); // подключим хуки
	}

	public function check_options_upd() {
		if (false == common_option_get('brpv_version')) { // это первая установка
			if (is_multisite()) {
				update_blog_option(get_current_blog_id(), 'brpv_version', $this->plugin_version);
			} else {
				update_option('brpv_version', $this->plugin_version);
			}
		} else {
			$this->set_new_options();
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
		// Если предыдущая версия плагина меньше текущей
		if (version_compare($this->get_plugin_version(), $this->plugin_version, '<')) { 

		} else { // обновления не требуются
			return;
		}
		
		// добавление новых опций
		if (brpv_optionGET('brpv_posts_type_arr') === false) {brpv_optionUPD('brpv_posts_type_arr', array('post', 'page'), '', 'yes');}
		if (brpv_optionGET('brpv_main_color') === false) {brpv_optionUPD('brpv_main_color', '#ffc000', '', 'yes');}
		if (brpv_optionGET('brpv_hover_color') === false) {brpv_optionUPD('brpv_hover_color', '#ff5500', '', 'yes');}

		if (is_multisite()) {
			update_blog_option(get_current_blog_id(), 'brpv_version', $this->plugin_version);
		} else {
			update_option('brpv_version', $this->plugin_version);
		}
		return;
	}

	public function init_classes() {
		return;
	}

	public function init_hooks() {		
		add_action('admin_init', [ $this, 'listen_submits_func' ], 10); // ещё можно слушать чуть раньше на wp_loaded
		add_action('admin_menu', [ $this, 'add_admin_menu_func' ]);

		add_action('wp_head',  [ $this, 'hover_styles_and_session_counter' ]); // вывод нужных стилей и cчетчик посещений
		add_action('wp_enqueue_scripts', [ $this, 'brpv_enqueue_fp' ]);
		add_action('wp_enqueue_scripts', [ $this, 'register_style_frontend' ]);
		add_action('admin_notices', [ $this, 'print_admin_notices_func' ]);
		add_action('wp_ajax_brpv_ajax_func',  [ $this, 'brpv_ajax_func' ]);
		add_action('wp_ajax_nopriv_brpv_ajax_func',  [ $this, 'brpv_ajax_func' ]);
		
		add_shortcode('pageviews', [ $this, 'brpv_pageviews_func' ]);
		add_shortcode('pageratings', [ $this, 'brpv_pageratings_func' ]); /* шорткод рейтинг поста */
		
		/* Регаем стили только для страницы настроек плагина */
		add_action('admin_init', function() {
			wp_register_style('brpv-admin-css', BRPV_PLUGIN_DIR_URL.'css/brpv.css');
		}, 9999);	

		add_filter('plugin_action_links', [ $this, 'add_plugin_action_links' ], 10, 2 );
	}

	public function listen_submits_func() {
		do_action('brpv_listen_submits');

		if (isset($_REQUEST['brpv_submit_action'])) {
			if (!empty($_POST) && check_admin_referer('brpv_nonce_action', 'brpv_nonce_field')) {
				if (is_multisite()) {
					if (isset($_POST['brpv_posts_type_arr'])) {
						update_blog_option(get_current_blog_id(), 'brpv_posts_type_arr', $_POST['brpv_posts_type_arr']);
					}
					if (isset($_POST['brpv_submit_action'])) {
						update_blog_option(get_current_blog_id(), 'brpv_not_count_bots', sanitize_text_field($_POST['brpv_not_count_bots']));
					}
					if (isset($_POST['brpv_main_color'])) {
						update_blog_option(get_current_blog_id(), 'brpv_main_color', sanitize_text_field($_POST['brpv_main_color']));
					}
					if (isset($_POST['brpv_hover_color'])) {
						update_blog_option(get_current_blog_id(), 'brpv_hover_color', sanitize_text_field($_POST['brpv_hover_color']));
					}
				} else {
					if (isset($_POST['brpv_posts_type_arr'])) {
						update_option('brpv_posts_type_arr', $_POST['brpv_posts_type_arr']);
					}
					if (isset($_POST['brpv_submit_action'])) {
						update_option('brpv_not_count_bots', sanitize_text_field($_POST['brpv_not_count_bots']));
					}
					if (isset($_POST['brpv_main_color'])) {
						update_option('brpv_main_color', sanitize_text_field($_POST['brpv_main_color']));
					}
					if (isset($_POST['brpv_hover_color'])) {
						update_option('brpv_hover_color', sanitize_text_field($_POST['brpv_hover_color']));
					}
				}
			}

			$message = __('Updated', 'best-rating-pageviews');
			$class = 'notice-success';
			add_action('admin_notices', function() use ($message, $class) { 
				$this->admin_notices_func($message, $class);
			}, 10, 2);
		}

		if (isset($_REQUEST['brpv_submit_clear_stat'])) {
			if (!empty($_POST) && check_admin_referer('brpv_nonce_action_clear_stat', 'brpv_nonce_clear_stat_field')) {
				$args = [
					'post_type' => ['post', 'page', 'product'],
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'relation' => 'AND',
					'orderby' => 'ID',
					'fields'  => 'ids',
					'meta_query' => [
						[
							'key' => '_brpv_pageviews',
							'compare' => 'EXISTS'
						]
					]
				];
				$res_query = new WP_Query($args);
				global $wpdb;
				if ($res_query->have_posts()) { 
					for ($i = 0; $i < count($res_query->posts); $i++) {
						delete_post_meta($res_query->posts[$i], '_brpv_ballov');
						delete_post_meta($res_query->posts[$i], '_brpv_golosov');
						delete_post_meta($res_query->posts[$i], '_brpv_lastime');
						delete_post_meta($res_query->posts[$i], '_brpv_pageviews');
						delete_post_meta($res_query->posts[$i], '_brpv_total_rating');
					}
				}

				$message = __('Statistics deleted', 'best-rating-pageviews');
				$class = 'notice-success';
				add_action('admin_notices', function() use ($message, $class) { 
					$this->admin_notices_func($message, $class);
				}, 10, 2);
			}
		}
	}

	// Добавляем пункты меню
	public function add_admin_menu_func() {
		$page_suffix = add_menu_page(null , __('Statistics', 'best-rating-pageviews'), 'unfiltered_html', 'brpv-statistics', [ $this, 'get_statistics_page_func' ], 'dashicons-chart-bar', 51);	
		add_action('admin_print_styles-'. $page_suffix, [ $this, 'enqueue_style_admin_css_func' ]); // создаём хук, чтобы стили выводились только на странице настроек

		$page_suffix = add_submenu_page('brpv-statistics', __('Settings', 'best-rating-pageviews'), __('Settings', 'best-rating-pageviews'), 'unfiltered_html', 'brpv-settings', [ $this, 'get_settings_page_func' ]);
		add_action('admin_print_styles-'. $page_suffix, [ $this, 'enqueue_style_admin_css_func' ]);

		// $page_subsuffix = add_submenu_page('brpvexport', __('Add Extensions', 'best-rating-pageviews'), __('Extensions', 'best-rating-pageviews'), 'manage_woocommerce', 'brpv-extensions', 'brpv_extensions_page');
		// require_once BRPV_PLUGIN_DIR_PATH.'/extensions.php';
		// add_action('admin_print_styles-'. $page_subsuffix, [ $this, 'enqueue_style_admin_css_func' ]);
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
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		add_action('admin_footer', [ $this, 'admin_footer_script' ], 99 );
	} 

	// Подключаем свой скрпит в подвал 
	public function admin_footer_script() { 
		// https://wp-kama.ru/id_4621/vyibora-tsveta-iris-color-picker-v-wordpress.html 
		// http://automattic.github.io/Iris/
		?>
		<script type="text/javascript">jQuery(document).ready(function($) { 
			var myOptions = {
				// устанавливает цвет по умолчанию, также цвет по умолчанию из атрибута value у input
				defaultColor: false,
				// функция обратного вызова, срабатывающая каждый раз при выборе цвета (когда водите мышкой по палитре)
				change: function(event, ui){ },
				// функция обратного вызова, срабатывающая при очистке (сбросе) цвета
				clear: function(){ },
				// спрятать ли выбор цвета при загрузке палитра будет появляться при клике
				hide: true,
				// показывать ли группу стандартных цветов внизу палитры 
				// можно добавить свои цвета указав их в массиве: ['#125', '#459', '#78b', '#ab0', '#de3', '#f0f']
				palettes: true
			}
			$('#brpv_main_color').wpColorPicker(myOptions);
			$('#brpv_hover_color').wpColorPicker(myOptions);
		});</script><?php
	} 
	
	public function hover_styles_and_session_counter() {
		if (is_multisite()) {
			$hover_color = get_blog_option(get_current_blog_id(), 'brpv_hover_color');
		} else {
			$hover_color = get_option('brpv_hover_color');
		}
		// печатаем стили
		echo '<style>.brpv_raiting:not(.hover_disabled) .brpv_raiting_icon:hover ~ .brpv_raiting_icon use, .brpv_raiting:not(.hover_disabled) .brpv_raiting_icon:hover use {fill: '.$hover_color.';}</style>';

		// https://habrahabr.ru/sandbox/74080/
		if (is_singular()) { // Функция объединяет в себе : is_single(), is_page(), is_attachment() и произвольные типы записей.
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
		if (get_post_meta($post->ID, '_brpv_pageviews', true)) {
			echo get_post_meta($post->ID, '_brpv_pageviews', true); /*.$_SERVER['HTTP_USER_AGENT'];*/
		} else {
			echo "0";
		}
	}	

	function brpv_pageratings_func() {
		global $post;		
		$post_id = (int)$post->ID;
		if (get_post_meta($post_id, '_brpv_total_rating', true)) {
			$rating_value = get_post_meta($post_id, '_brpv_total_rating', true);
		} else {
			$rating_value = 0;
		}
		$half_mask_id = 'half_'.$post_id;
		if ($rating_value > 0) {
			$full_stars_count = (int)$rating_value; // сколько целых звёзд
			$half_mask_value_x = round(fmod($rating_value, 1)*100, 2); // какой процент зведы закрасить
		}	
		/* число голосов */
		if (get_post_meta($post_id, '_brpv_golosov', true)) {
			$rating_count = (int)get_post_meta($post_id, '_brpv_golosov', true);
		} else {
			$rating_count = 0;
		}
		if (is_multisite()) {
			$main_color = get_blog_option(get_current_blog_id(), 'brpv_main_color');
			// $hover_color = get_blog_option(get_current_blog_id(), 'brpv_hover_color');
		} else {
			$main_color = get_option('brpv_main_color');
			// $hover_color = get_option('brpv_hover_color');
		}
		$item_reviewed = esc_html($post->post_title); 
		$cookie_name = 'article'.$post_id; ?>
		<svg width="0" height="0" viewBox="0 0 32 32">
			<defs>
				<mask id="<?php echo $half_mask_id; ?>">
					<rect x="0" y="0" width="32" height="32" fill="white" />
					<rect x="<?php echo $half_mask_value_x; ?>%" y="0" width="32" height="32" fill="grey" />
				</mask>
				<symbol viewBox="0 0 32 32" id="star">
					<path d="M31.547 12a.848.848 0 00-.677-.577l-9.427-1.376-4.224-8.532a.847.847 0 00-1.516 0l-4.218 8.534-9.427 1.355a.847.847 0 00-.467 1.467l6.823 6.664-1.612 9.375a.847.847 0 001.23.893l8.428-4.434 8.432 4.432a.847.847 0 001.229-.894l-1.615-9.373 6.822-6.665a.845.845 0 00.214-.869z" />
				</symbol>
			</defs>
		</svg>
		<div style="display: none;" itemprop="aggregateRating" itemscope="" itemtype="https://schema.org/AggregateRating"><meta itemprop="bestRating" content="5"><meta itemprop="ratingValue" content="<?php echo $rating_value; ?>"><meta itemprop="ratingCount" content="<?php echo $rating_count; ?>"><meta itemprop="itemReviewed" content="<?php echo $item_reviewed; ?>"></div>
		<div id="brpv_raiting_star_<?php echo $post_id; ?>" class="brpv_raiting<?php if (isset($_COOKIE[$cookie_name])) {echo ' hover_disabled';} ?>">
			<div class="brpv_raiting_info"><strong><?php _e('Raiting', 'best-rating-pageviews'); ?>:</strong> <span class="brpv_raiting_value"><?php echo $rating_value; ?></span></div>
			<?php for ($i = 5; $i > 0; $i--) : ?>
				<svg class="brpv_raiting_icon" width="32" height="32" viewBox="0 0 32 32" data-rating="<?php echo $i; ?>" post-id="<?php echo $post_id; ?>">
					<?php if ($i-1 > $full_stars_count) : ?>
						<use xlink:href="#star" fill="grey"></use>
					<?php elseif ($i-1 == $full_stars_count) : ?>
						<use xlink:href="#star" fill="<?php echo $main_color; ?>" mask="url(#<?php echo $half_mask_id; ?>)"></use>
					<?php else : ?>
						<use xlink:href="#star" fill="<?php echo $main_color; ?>"></use>
					<?php endif; ?>
				</svg>
			<?php endfor; ?>
		</div>
		<?php 
	}
	/* end шорткод рейтинг поста */ 
 
	/* Функция Аякс обработчика рейтинга */
	public function brpv_ajax_func() {		
		$result = [ ];
		if (isset($_REQUEST['user_votes']) && isset($_REQUEST['postId'])) {
			$user_votes = (int)sanitize_text_field($_REQUEST['user_votes']); // получаем оценку, которую поставил пользователь	
			$post_id = (int)sanitize_text_field($_REQUEST['postId']); // id поста, которому поставили оценку
		} else {$result['status'] = "false"; $result = json_encode($result); echo $result; die();} 
		
		$this->fixer($post_id);

		if (get_post_meta($post_id, '_brpv_golosov', true)) { 
			$golosov = (int)get_post_meta($post_id, '_brpv_golosov', true); 
		} else {$golosov = (int)0;}
		if (get_post_meta($post_id, '_brpv_ballov', true)) { 
			$ballov = (int)get_post_meta($post_id, '_brpv_ballov', true); 
		} else {$ballov = (int)0;}
		if (get_post_meta($post_id, '_brpv_total_rating', true)) {
			$total_rating = (int)get_post_meta($post_id, '_brpv_total_rating', true);
		} else {$total_rating = (int)0;}
		
		$golosov_new = $golosov + 1; // нове значение проголосовавших
		$ballov_new = $ballov + $user_votes; // нове значение баллов
		$total_rating_new = $ballov_new / $golosov_new; // общая оценка
		$total_rating_new = round($total_rating_new, 2); // округляем до сотых
		
		update_post_meta($post_id, '_brpv_golosov', $golosov_new);
		update_post_meta($post_id, '_brpv_ballov', $ballov_new);
		update_post_meta($post_id, '_brpv_total_rating', $total_rating_new);

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
		wp_register_style('brpv_style', BRPV_PLUGIN_DIR_URL . 'css/brpv-rating.css', '', null, 'all' );
		wp_enqueue_style('brpv_style', '', '', '', true); // подключаем в футре
	}
	
	// регистрируем скрипты для внешней части сайта
	public function brpv_enqueue_fp() { 
		wp_register_script('brpv_rating', BRPV_PLUGIN_DIR_URL . 'js/brpv-rating.js');
		wp_enqueue_script('brpv_rating', '', '', [ 'jquery' ], true); // подключаем в футре
		wp_register_script('brpv_jquery_cookiess', BRPV_PLUGIN_DIR_URL . 'js/jquery.cookies.js');
		wp_enqueue_script('brpv_jquery_cookiess', '', '', [ 'jquery' ], true);
		wp_localize_script('brpv_rating', 'brpvajax', [ 'brpvajaxurl' => admin_url('admin-ajax.php') ] );
	} 
 
	public function print_admin_notices_func() {
		return;
	}

	public function add_plugin_action_links($actions, $plugin_file) {
		if (false === strpos($plugin_file, BRPV_PLUGIN_BASENAME)) { // проверка, что у нас текущий плагин
			return $actions;
		}
		$settings_link = '<a href="/wp-admin/admin.php?page=brpv-settings">'. __('Settings', 'best-rating-pageviews').'</a>';
		array_unshift($actions, $settings_link);
		return $actions;
	}

	/* Функция увеличения счетчика просмотров */
	private function add_pageviews() {
		global $user_ID, $post;	
		if (is_multisite()) {
			$brpv_posts_type_arr = get_blog_option(get_current_blog_id(), 'brpv_posts_type_arr');
		} else {
			$brpv_posts_type_arr = get_option('brpv_posts_type_arr');
		}
		$post_id = (int)$post->ID; // получаем id поста
		$post_type = get_post_type($post_id);
		if (!in_array($post_type, $brpv_posts_type_arr)) {return;}

		$this->fixer($post_id);

		$lastime = current_time('timestamp');
		$pageviews = (int)get_post_meta($post_id, '_brpv_pageviews', true); // получаем число постов
		update_post_meta($post_id, '_brpv_pageviews', ($pageviews + 1));
		update_post_meta($post_id, '_brpv_lastime', $lastime);
	}

	private function admin_notices_func($message, $class) {
		printf('<div class="notice %1$s"><p>%2$s</p></div>', $class, $message);
		return;
	}

	private function fixer($post_id) {
		if (get_post_meta($post_id, 'brpv_ballov', true) !== '') {
			update_post_meta($post_id, '_brpv_ballov', get_post_meta($post_id, 'brpv_ballov', true));
			delete_post_meta($post_id, 'brpv_ballov');
		}
		if (get_post_meta($post_id, 'brpv_golosov', true) !== '') {
			update_post_meta($post_id, '_brpv_golosov', get_post_meta($post_id, 'brpv_golosov', true));
			delete_post_meta($post_id, 'brpv_golosov');
		}	
		if (get_post_meta($post_id, 'brpv_lastime', true) !== '') {
			update_post_meta($post_id, '_brpv_lastime', get_post_meta($post_id, 'brpv_lastime', true));
			delete_post_meta($post_id, 'brpv_lastime');
		}	
		if (get_post_meta($post_id, 'brpv_pageviews', true) !== '') {
			update_post_meta($post_id, '_brpv_pageviews', get_post_meta($post_id, 'brpv_pageviews', true));
			delete_post_meta($post_id, 'brpv_pageviews');
		}		
		if (get_post_meta($post_id, 'brpv_total_rating', true) !== '') {
			update_post_meta($post_id, '_brpv_total_rating', get_post_meta($post_id, 'brpv_total_rating', true));
			delete_post_meta($post_id, 'brpv_total_rating');
		}
	}
} /* end class BestRatingPageviews */