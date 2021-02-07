<?php if (!defined('ABSPATH')) {exit;}
/*
* @since 2.0.0
*
* @param string $optName (require)
* @param string $value (require)
* @param string $n (not require)
* @param string $autoload (not require)
*
* @return true/false
* Возвращает то, что может быть результатом add_blog_option, add_option
*/
function brpv_optionADD($optName, $value='', $n='', $autoload = 'yes') {
 if ($optName == '') {return false;}
 if ($n === '1') {$n='';}
 $optName = $optName.$n;
 if (is_multisite()) { 
	return add_blog_option(get_current_blog_id(), $optName, $value);
 } else {
	return add_option($optName, $value, '', $autoload);
 }
}
/*
* @since 2.0.0
*
* @param string $optName (require)
* @param string $value (require)
* @param string $n (not require)
* @param string $autoload (not require)
*
* @return true/false
* Возвращает то, что может быть результатом update_blog_option, update_option
*/
function brpv_optionUPD($optName, $value='', $n='', $autoload = 'yes') {
 if ($optName == '') {return false;}
 if ($n === '1') {$n='';}
 $optName = $optName.$n;
 if (is_multisite()) { 
	return update_blog_option(get_current_blog_id(), $optName, $value);
 } else {
	return update_option($optName, $value, $autoload);
 }
}
/*
* @since 2.0.0
*
* @param string $optName (require)
* @param string $n (not require)
*
* @return Значение опции или false
* Возвращает то, что может быть результатом get_blog_option, get_option
*/
function brpv_optionGET($optName, $n='') {
 if ($optName == '') {return false;}
 if ($n === '1') {$n='';}
 $optName = $optName.$n;
 if (is_multisite()) { 
	return get_blog_option(get_current_blog_id(), $optName);
 } else {
	return get_option($optName);
 }
}
/*
* @since 2.0.0
*
* @param string $optName (require)
* @param string $n (not require)
*
* @return true/false
* Возвращает то, что может быть результатом delete_blog_option, delete_option
*/
function brpv_optionDEL($optName, $n='') {
 if ($optName == '') {return false;}
 if ($n === '1') {$n='';}   
 $optName = $optName.$n;
 if (is_multisite()) { 
	return delete_blog_option(get_current_blog_id(), $optName);
 } else {
	return delete_option($optName);
 }
} 
/*
* @since 2.0.0
*
* @return nothing
*
* Updates plugin settings
*/
function brpv_set_new_options() {
	wp_clean_plugins_cache();
	wp_clean_update_cache();
	add_filter('pre_site_transient_update_plugins', '__return_null');
	wp_update_plugins();
	remove_filter('pre_site_transient_update_plugins', '__return_null');

	if (brpv_optionGET('brpv_version') === false) {brpv_optionUPD('2.0.0', '', '', 'no');}

	if (defined('brpv_VER')) {
		if (is_multisite()) {
			update_blog_option(get_current_blog_id(), 'brpv_version', brpv_VER);
		} else {
			update_option('brpv_version', brpv_VER);
		}
	}
}
?>