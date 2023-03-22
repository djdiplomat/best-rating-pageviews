<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Name: Best Rating & Pageviews
* Plugin URI: https://icopydoc.ru/category/documentation/
* Description: Add Star rating, pageviews and adds a tool for analyzing the effectiveness of content. Also this plugin adds a widget which shows popular posts and pages based on the rating and pageviews.
* Version: 3.0.3
* Requires at least: 4.5
* Requires PHP: 5.6
* Author: Maxim Glazunov
* Author URI: https://icopydoc.ru
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: best-rating-pageviews
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
* Copyright 2018-2023 (Author emails: djdiplomat@yandex.ru, support@icopydoc.ru)
*/ 
$nr = false;
// Check php version
if (version_compare(phpversion(), '5.6.0', '<')) { // не совпали версии
	add_action('admin_notices', function() {
		warning_notice('notice notice-error', 
			sprintf(
				'<strong style="font-weight: 700;">%1$s</strong> %2$s 5.6.0 %3$s %4$s',
				'Best Rating & Pageviews',
				__('plugin requires a php version of at least', 'best-rating-pageviews'),
				__('You have the version installed', 'best-rating-pageviews'),
				phpversion()
			)
		);
	});
	$nr = true;
}

/**
 * @since	0.1.0
 * 
 * @param	string			$class (not require)
 * @param	string 			$message (not require)
 * 
 * @return	string/nothing
 * 
 * Display a notice in the admin Plugins page. Usually used in a @hook 'admin_notices'
 */
if (!function_exists('warning_notice')) {
	function warning_notice($class = 'notice', $message = '') {
		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	}
}

// Define constants
$upload_dir = wp_get_upload_dir();
define('BRPV_SITE_UPLOADS_URL', $upload_dir['baseurl']); // http://site.ru/wp-content/uploads
define('BRPV_SITE_UPLOADS_DIR_PATH', $upload_dir['basedir']); // /home/site.ru/public_html/wp-content/uploads

define('BRPV_PLUGIN_VERSION', '3.0.3'); // 0.1.0
define('BRPV_PLUGIN_UPLOADS_DIR_URL', $upload_dir['baseurl'].'/best-rating-pageviews'); // http://site.ru/wp-content/uploads/best-rating-pageviews
define('BRPV_PLUGIN_UPLOADS_DIR_PATH', $upload_dir['basedir'].'/best-rating-pageviews'); // /home/site.ru/public_html/wp-content/uploads/best-rating-pageviews
define('BRPV_PLUGIN_DIR_URL', plugin_dir_url(__FILE__)); // http://site.ru/wp-content/plugins/best-rating-pageviews/
define('BRPV_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__)); // /home/p135/www/site.ru/wp-content/plugins/best-rating-pageviews/
define('BRPV_PLUGIN_MAIN_FILE_PATH', __FILE__); // /home/p135/www/site.ru/wp-content/plugins/best-rating-pageviews/best-rating-pageviews.php
define('BRPV_PLUGIN_SLUG', wp_basename(dirname(__FILE__))); // best-rating-pageviews - псевдоним плагина
define('BRPV_PLUGIN_BASENAME', plugin_basename(__FILE__)); // best-rating-pageviews/best-rating-pageviews.php - полный псевдоним плагина (папка плагина + имя главного файла)
// $nr = apply_filters('brpv_f_nr', $nr);
unset($upload_dir);

// load translation
add_action('plugins_loaded', function() {
	load_plugin_textdomain('best-rating-pageviews', false, dirname(BRPV_PLUGIN_BASENAME).'/languages/');
});

if (false === $nr) {
	unset($nr);
	require_once BRPV_PLUGIN_DIR_PATH.'/packages.php';
	register_activation_hook(__FILE__, ['BestRatingPageviews', 'on_activation']);
	register_deactivation_hook(__FILE__, ['BestRatingPageviews', 'on_deactivation']);
	add_action('plugins_loaded', ['BestRatingPageviews', 'init'], 10); // активируем плагин
	define('BRPV_ACTIVE', true);
} 