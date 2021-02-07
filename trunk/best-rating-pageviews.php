<?php
/*
Plugin Name: Best Rating & Pageviews
Description: Add Star rating, pageviews and adds a tool for analyzing the effectiveness of content. Also this plugin adds a widget which shows popular posts and pages based on the rating and pageviews.
Tags: rating, stars, pageviews, widget, popular  
Author: Maxim Glazunov
Author URI: https://icopydoc.ru
License: GPLv2
Version: 2.0.0
Text Domain: best-rating-pageviews
Domain Path: /languages/
*/
/*	Copyright YEAR PLUGIN_AUTHOR_NAME (email : djdiplomat@yandex.ru)
 
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.
 
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
 
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
require_once plugin_dir_path(__FILE__).'/functions.php'; // Подключаем файл функций
register_activation_hook(__FILE__, array('BestRatingPageviews', 'on_activation'));
register_deactivation_hook(__FILE__, array('BestRatingPageviews', 'on_deactivation'));
register_uninstall_hook(__FILE__, array('BestRatingPageviews', 'on_uninstall'));
add_action('plugins_loaded', array('BestRatingPageviews', 'init'));
add_action('plugins_loaded', 'brpv_load_plugin_textdomain'); // load translation
function brpv_load_plugin_textdomain() {
 load_plugin_textdomain('brpv', false, dirname(plugin_basename(__FILE__)).'/languages/');
}
class BestRatingPageviews {
 protected static $instance;
 public static function init() {
	is_null( self::$instance ) AND self::$instance = new self;
	return self::$instance;
 }

 public function __construct() {
	// brpv_DIR contains /home/p135/www/site.ru/wp-content/plugins/myplagin/
	define('brpv_DIR', plugin_dir_path(__FILE__)); 
	// brpv_URL contains http://site.ru/wp-content/plugins/myplagin/
	define('brpv_URL', plugin_dir_url(__FILE__));
	define('brpv_VER', '2.0.0');
	$brpv_version = brpv_optionGET('brpv_version');
  	if ($brpv_version !== brpv_VER) {brpv_set_new_options();} // автообновим настройки, если нужно	
	
	add_action('admin_menu', array($this, 'add_admin_menu'));
	add_action('wp_head',  array($this, 'brpv_pageviews')); // cчетчик посещений
	add_action('wp_enqueue_scripts', array($this, 'brpv_enqueue_fp'));
	add_action('wp_enqueue_scripts', array($this, 'brpv_register_style_frontend'));
	add_action('admin_notices', array($this, 'brpv_admin_notices_function'));
	add_action('wp_ajax_brpv_ajax_func',  array($this, 'brpv_ajax_func'));
	add_action('wp_ajax_nopriv_brpv_ajax_func',  array($this, 'brpv_ajax_func'));
//	add_action('wp_dashboard_setup', array($this, 'brpvrating_widgets'));
	
	add_shortcode('pageviews', array($this, 'brpv_pageviews_func'));
	add_shortcode('pageratings', array($this, 'brpv_pageratings_func')); /* шорткод рейтинг поста */
	
	/* Регаем стили только для страницы настроек плагина	*/
	add_action('admin_init', function() {
		wp_register_style('brpv-admin-css', plugins_url('css/brpv.css', __FILE__));
	}, 9999);	

	add_filter('plugin_action_links', array($this, 'brpv_plugin_action_links'), 10, 2 );
 }

 public static function brpv_admin_css_func() {
	/* Ставим css-файл в очередь на вывод */
	wp_enqueue_style('brpv-admin-css');
 } 

 public static function brpv_admin_head_css_func() {
	/* печатаем css в шапке админки */
	print '<style>/* Best Rating & Pageviews */
		.icp_img1 {background-image: url('. brpv_URL .'/img/sl1.jpg);}
		.icp_img2 {background-image: url('. brpv_URL .'/img/sl2.jpg);}
		.icp_img3 {background-image: url('. brpv_URL .'/img/sl3.jpg);}
		.icp_img4 {background-image: url('. brpv_URL .'/img/sl4.jpg);}
		.icp_img5 {background-image: url('. brpv_URL .'/img/sl5.jpg);}
		.icp_img6 {background-image: url('. brpv_URL .'/img/sl6.jpg);}
		.icp_img7 {background-image: url('. brpv_URL .'/img/sl7.jpg);}
	</style>';
 }
/*
 function brpvrating_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('brpvrating_widget', __( 'Rating & PageViews', 'brpv'), array($this, 'brpv_rating_widgets_info'));
 } 
 public function brpv_rating_widgets_info() {
	
 } 
*/
 public static function on_activation() { 
	if (is_multisite()) {
		add_blog_option(get_current_blog_id(), 'brpv_version', '2.0.0');
		add_blog_option(get_current_blog_id(), 'brpv_debug', 'true');
		add_blog_option(get_current_blog_id(), 'brpv_not_count_bots', 'yes');
		add_blog_option(get_current_blog_id(), 'brpv_rating_icons', 'brpv_pic1');
	} else {
		add_option('brpv_version', '2.0.0', '', 'no');
		add_option('brpv_debug', 'true');
		add_option('brpv_not_count_bots', 'yes'); // Учитывать ботов?	
		add_option('brpv_rating_icons', 'brpv_pic1');
	}	
 } 
 public static function on_deactivation() {
	 
 } 
 public static function on_uninstall() {
	if (is_multisite()) {
		delete_blog_option(get_current_blog_id(), 'brpv_version');
		delete_blog_option(get_current_blog_id(), 'brpv_debug');
		delete_blog_option(get_current_blog_id(), 'brpv_not_count_bots');
		delete_blog_option(get_current_blog_id(), 'brpv_rating_icons');
	} else {
		delete_option('brpv_version');
		delete_option('brpv_debug');
		delete_option('brpv_not_count_bots');
		delete_option('brpv_rating_icons');		
	}	  
 }
 // Register the management page
 public function add_admin_menu() {
	add_menu_page(null , __('Statistics', 'brpv'), 'manage_options', 'brpvstatistics', 'brpv_statistics_page', 'dashicons-chart-bar', 51);
	require_once brpv_DIR.'/statistics.php'; // Подключаем файл настроек
	
	$page_suffix = add_submenu_page('brpvstatistics', __('Settings', 'brpv'), __('Settings', 'brpv'), 'manage_options', 'brpvsettings', 'brpv_settings_page');
	require_once brpv_DIR.'/settings.php'; // Подключаем файл настроек
	
	add_action('admin_print_styles-' . $page_suffix, array($this, 'brpv_admin_css_func'));
 	add_action('admin_print_styles-' . $page_suffix, array($this, 'brpv_admin_head_css_func'));	
 } 
 
 public function brpv_pageviews() {
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
			$this->brpv_add_pageviews(); 
		}
	} else {
		// если ботов пропускать не нужно - добавляем счетчик
		$this->brpv_add_pageviews();
	}
  }
  return true;
 }
 
 /* Функция увеличения счетчика просмотров */
 private function brpv_add_pageviews() {
	global $user_ID, $post;		
	$postId = (int)$post->ID; // получаем id поста
	$lastime = current_time('timestamp');
	$pageviews = (int)get_post_meta($postId, 'brpv_pageviews', true); // получаем число постов
	update_post_meta($postId, 'brpv_pageviews', ($pageviews+1));
	update_post_meta($postId, 'brpv_lastime', $lastime);
 }
 /* end Функция увеличения счетчика просмотров */

 
 public function brpv_pageviews_func() {
	global $post;
	if (get_post_meta($post->ID, 'brpv_pageviews', true)) {
		echo get_post_meta($post->ID, 'brpv_pageviews', true); /*.$_SERVER['HTTP_USER_AGENT'];*/
	} else {
		echo "0";
	}
 }	

 function brpv_pageratings_func(){ 
	global $post;
	$postId = (int)$post->ID; 
	if (get_post_meta($postId, 'brpv_total_rating', true)) {
		$ratingValue = get_post_meta($postId, 'brpv_total_rating', true);
	} else {
		$ratingValue = 0;
	}	
	/* число голосов */
	if (get_post_meta($postId, 'brpv_golosov', true)) {
		$ratingCount = (int)get_post_meta($postId, 'brpv_golosov', true);
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
	<div class="brpv_raiting_star_<?php echo $postId; ?>">
		<div class="raiting">
			<div class="raiting_blank <?php echo $rating_icons; ?>"></div>
			<div class="raiting_hover <?php echo $rating_icons; ?>"></div>
			<div class="raiting_votes <?php echo $rating_icons; ?>"></div>
		</div>
		<div class="raiting_info"><img src="<?php echo brpv_URL.'img/'; ?>load.gif" /><strong><?php _e('Raiting', 'brpv'); ?>:</strong> <span class="brpv_raiting_value"></span></div>
		<div style="display: none;" class="hidden" postid="<?php echo $postId; ?>" ratingvalue="<?php echo $ratingValue; ?>"></div>
	</div><?php 
 }
 /* end шорткод рейтинг поста */ 
 
 /* Функция Аякс обработчика рейтинга */
 public function brpv_ajax_func(){		
	$result = array();
	if (isset($_REQUEST['user_votes']) && isset($_REQUEST['postId'])) {
		$user_votes = (int)sanitize_text_field($_REQUEST['user_votes']); // получаем оценку, которую поставил пользователь	
		$postId = (int)sanitize_text_field($_REQUEST['postId']); // id поста, которому поставили оценку
	} else {$result['status'] = "false"; $result = json_encode($result); echo $result; die();} 
	
	if (get_post_meta($postId, 'brpv_golosov', true)) { 
		$golosov = (int)get_post_meta($postId, 'brpv_golosov', true); 
	} else {$golosov = (int)0;}
	if (get_post_meta($postId, 'brpv_ballov', true)) { 
		$ballov = (int)get_post_meta($postId, 'brpv_ballov', true); 
	} else {$ballov = (int)0;}
	if (get_post_meta($postId, 'brpv_total_rating', true)) {
		$total_rating = (int)get_post_meta($postId, 'brpv_total_rating', true);
	} else {$total_rating = (int)0;}
	 
	$golosov_new = $golosov + 1; // нове значение проголосовавших
	$ballov_new = $ballov + $user_votes; // нове значение баллов
	$total_rating_new = $ballov_new / $golosov_new; // общая оценка
	$total_rating_new = round($total_rating_new, 2); // округляем до сотых
	 
	update_post_meta($postId, 'brpv_golosov', $golosov_new);
	update_post_meta($postId, 'brpv_ballov', $ballov_new);
	update_post_meta($postId, 'brpv_total_rating', $total_rating_new);

	$result['user_votes'] = $user_votes;
	$result['postId'] = $postId;
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
 public function brpv_register_style_frontend() {	 
	wp_register_style('brpv_style', brpv_URL . 'css/rating.css', '', null, 'all' );
	wp_enqueue_style('brpv_style', '', '', '', true); // подключаем в футре
 }
 
 //регистрируем скрипты для внешней части сайта
 public function brpv_enqueue_fp() { 
	wp_register_script('brpv_rating', brpv_URL . 'js/rating.js');
	wp_enqueue_script('brpv_rating', '', '', array('jquery'), true); // подключаем в футре
	wp_register_script('brpv_jquery_cookiess', brpv_URL . 'js/jquery.cookies.js');
	wp_enqueue_script('brpv_jquery_cookiess', '', '', array('jquery'), true);
	wp_localize_script('brpv_rating', 'brpvajax', array('brpvajaxurl' => admin_url('admin-ajax.php')));	
 } 
 
 public function brpv_admin_notices_function() {
	if (isset($_REQUEST['brpv_submit_action'])) {
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
			print '<div class="updated notice notice-success is-dismissible"><p>'. __('Updated', 'brpv'). '.</p></div>';
		}
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
			print '<div class="updated notice notice-success is-dismissible"><p>'. __('Statistics deleted', 'brpv'). '</p></div>';
		}
	}
 }

 public static function brpv_plugin_action_links($actions, $plugin_file) {
	if (false === strpos($plugin_file, basename(__FILE__))) {
		// проверка, что у нас текущий плагин
		return $actions;
	}	
	$settings_link = '<a href="/wp-admin/admin.php?page=brpvsettings">'. __('Settings', 'brpv').'</a>';
	array_unshift($actions, $settings_link); 
	return $actions; 
 }
} /* end class BestRatingPageviews */





/* ВИДЖЕТ ПОПУЛЯРНОЕ */
class brpv_widget_popular extends WP_Widget {
 public function __construct() {
	parent::__construct("text_widget", __( 'Popular', 'brpv' ),
		array( 'description' => __( 'Shows popular posts and pages based on the rating and pageviews', 'brpv' ), ));
 }	
 //Метод form() (отвечает за внешний вид виджета в админке)
 public function form($instance) {
	$title = __( 'Popular', 'brpv' ); // дефольный заголовок
	$NumPostov = "5"; // дефолтное число постов
	$WhatShows = "post";
	$order = "ASC";
	$orderby = "brpv_pageviews";
	// если instance не пустой, достанем значения
	if (!empty($instance)) {
		$title = $instance["title"];
		$NumPostov = $instance["NumPostovId"];
		$WhatShows = $instance["WhatShowsId"];
		$order = $instance["orderId"];
		$orderby = $instance["orderbyId"];
	}
		
	/* вытаскиваем первый параметр (заголовок виджета) */
	$tableId = $this->get_field_id("title");
	$tableName = $this->get_field_name("title");
	echo '<p><label for="' . $tableId . '">'.__( "Title", "brpv" ).':</label>';
	echo '<input class="widefat" id="' . $tableId . '" type="text" name="' .
	$tableName . '" value="' . $title . '"></p>';
		
	/* вытаскиваем второй параметр (число постов в виджете) */
	$NumPostovId = $this->get_field_id("NumPostovId");
	$NumPostovName = $this->get_field_name("NumPostovId");
	echo '<p><label for="' . $NumPostovId . '">'.__( "Num Posts", "brpv" ).': </label><input class="tiny-text" size="3" step="1" min="1" id="' . $NumPostovId . '" type="number" name="' .
	$NumPostovName . '" value="' . $NumPostov . '"></p>';
	
	/* вытаскиваем третий параметр (что выводить) */
	$WhatShowsId = $this->get_field_id("WhatShowsId");
	$WhatShowsName = $this->get_field_name("WhatShowsId");?>
	<p><label for="<?php echo $WhatShowsId; ?>"><?php _e( 'Show', 'brpv' ); ?>:</label>
	<select id="<?php $WhatShowsId; ?>" class="widefat" name="<?php
	echo $WhatShowsName; ?>">
		<option value="post" <?php echo ($WhatShows == 'post') ? ' selected="selected"' : '' ?>><?php _e( 'Post', 'brpv' ); ?></option>
		<option value="page" <?php echo ($WhatShows == 'page') ? ' selected="selected"' : '' ?>><?php _e( 'Page', 'brpv'); ?></option>
	</select></p>	
	<?php
	
	/* вытаскиваем четвертый параметр (сортировка) */
	$orderId = $this->get_field_id("orderId");
	$orderName = $this->get_field_name("orderId"); ?>
	<p><label for="<?php echo $orderId; ?>"><?php _e( 'Order', 'brpv' ); ?>:</label>
	<select id="<?php $orderId; ?>" class="widefat" name="<?php
	echo $orderName; ?>">
		<option value="ASC" <?php echo ($order == 'ASC') ? ' selected="selected"' : '' ?>><?php _e( 'ASC', 'brpv' ); ?></option>
		<option value="DESC" <?php echo ($order == 'DESC') ? ' selected="selected"' : '' ?>><?php _e( 'DESC', 'brpv'); ?></option>
	</select></p>	
	<?php 
	
	/* вытаскиваем пятый параметр (ключ сортировки) */
	$orderbyId = $this->get_field_id("orderbyId");
	$orderbyName = $this->get_field_name("orderbyId"); ?>
	<p><label for="<?php echo $orderbyId; ?>"><?php _e( 'Order by', 'brpv' ); ?>:</label>
	<select id="<?php $orderbyId; ?>" class="widefat" name="<?php
	echo $orderbyName; ?>">
		<option value="brpv_pageviews" <?php echo ($orderby == 'brpv_pageviews') ? ' selected="selected"' : '' ?>><?php _e( 'PageViews', 'brpv' ); ?></option>
		<option value="brpv_total_rating" <?php echo ($orderby == 'brpv_total_rating') ? ' selected="selected"' : '' ?>><?php _e( 'Rating', 'brpv'); ?></option>
	</select></p>	
	<?php 
 }
 //Метод update() (отвечает за обновление параметров)
 public function update($newInstance, $oldInstance) {
	$values = array();
	$values["title"] = htmlentities($newInstance["title"]); // обновляем заголовок
	$values["NumPostovId"] = htmlentities($newInstance["NumPostovId"]); // обновляем число постов
	$values["WhatShowsId"] = htmlentities($newInstance["WhatShowsId"]); // обновляем что выводить
	$values["orderId"] = htmlentities($newInstance["orderId"]); // обновляем сортировку
	$values["orderbyId"] = htmlentities($newInstance["orderbyId"]); // обновляем ключ сортировки
	return $values;
 }
	
 //Метод widget() (отвечает за вывод виджета на сайте)
 public function widget($args, $instance) {
	/* получение параметров */
	$title = $instance["title"]; // получаем заголовок
	$NumPostov = $instance["NumPostovId"]; //получаем число постов
	$WhatShows = $instance["WhatShowsId"]; // что выводить
	$order = $instance["orderId"]; // сортировка
	$orderby = $instance["orderbyId"]; // ключ сортировки
		
	echo $args['before_widget']; // вывод обертки виджета (открывающий тег)
	/* Выводт виджета */
	if (!empty( $title )) { echo $args['before_title'] . $title . $args['after_title'];} // выводим заголовок виджета в оберткие $args['after_title']
	
	$argums = array(
		'meta_key' => $orderby,
		'post_type' => array($WhatShows),
		'showposts' => $NumPostov,
		'posts_per_page' => -1,
		'orderby' => $orderby,
		'order' => $order,
		'post_status' => 'publish',
	);
	$t_dir = get_bloginfo('template_directory'); // в $t_dir храним урл директории шаблона
	query_posts($argums);
	$brpv = new WP_Query($argums);
	if($brpv->have_posts()) : ?>
		<ul>
			<?php while($brpv->have_posts()):
				$brpv->the_post();
				$postId = get_the_ID(); ?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
		</ul>
	<?php endif;
	wp_reset_postdata(); // восстанавливаем глобальную переменную $post
	/* End Выводт виджета*/
	echo $args['after_widget']; // вывод обертки виджета (закрывающий тег)
 }
}
add_action("widgets_init", function () {
 register_widget("brpv_widget_popular");
});
/* END ВИДЖЕТ ПОПУЛЯРНОЕ */
?>