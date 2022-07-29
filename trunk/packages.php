<?php if (!defined('ABSPATH')) {exit;} 
require_once plugin_dir_path(__FILE__).'includes/old-php-add-functions.php';
require_once plugin_dir_path(__FILE__).'includes/icopydoc-useful-functions.php';
// require_once plugin_dir_path(__FILE__).'includes/wc-add-functions.php';
require_once plugin_dir_path(__FILE__).'functions.php'; // Подключаем файл функций
require_once plugin_dir_path(__FILE__).'includes/backward-compatibility.php';

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
require_once plugin_dir_path(__FILE__).'classes/system/class-brpv-wp-list-table.php';
require_once plugin_dir_path(__FILE__).'classes/system/class-brpv-settings-feed-wp-list-table.php';
require_once plugin_dir_path(__FILE__).'classes/system/class-brpv-feedback.php';
require_once plugin_dir_path(__FILE__).'classes/system/class-brpv-error-log.php';
require_once plugin_dir_path(__FILE__).'classes/system/class-brpv-settings-page.php';
require_once plugin_dir_path(__FILE__).'classes/system/class-brpv-statistics-page.php';
require_once plugin_dir_path(__FILE__).'classes/widgets/class-brpv-widget-popular.php';
?>