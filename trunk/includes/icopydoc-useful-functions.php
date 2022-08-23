<?php if (!defined('ABSPATH')) {exit;}
// 1.1.1 (23-08-2022)
// Maxim Glazunov (https://icopydoc.ru)
// This code adds several useful functions to the WordPress.

/**
* @since 1.0.0
*
* @param 
*	array/string/obj	$text (require)
* @param string 		$new_line (not require)
* @param int 			$i (not require)
* @param string 		$res (not require)
*
* @return string
*
* Converts an array to an easy-to-read format
*/
if (!function_exists('get_array_as_string')) {
	function get_array_as_string($text, $new_line = PHP_EOL, $i = 0, $res = '') {
		$tab = ''; for ($x = 0; $x < $i; $x++) {$tab = '---'.$tab;}
		if (is_object($text)) {$text = (array)$text;}
		if (is_array($text)) { 
			$i++;
			foreach ($text as $key => $value) {
				if (is_array($value)) {	// массив
					$res .= $new_line .$tab."[$key] => (".gettype($value).")";
					$res .= $tab.get_array_as_string($value, $new_line, $i);
				} else { // не массив
					$res .= $new_line .$tab."[$key] => (".gettype($value).")". $value;
				}
			}
		} else {
		   $res .= $new_line .$tab.$text;
		}
		return $res;
	}	
}

/**
* @since 1.0.0
*
* @param string 		$url (require)
* @param string 		$whot (not require)
*
* @return string/false
*
* Return URL without GET parameters or just GET parameters without URL
*/
if (!function_exists('get_from_url')) {
	function get_from_url($url, $whot = 'url') {
		$url = str_replace("&amp;", "&", $url); // Заменяем сущности на амперсанд, если требуется
		list($url_part, $get_part) = array_pad(explode("?", $url), 2, ""); // Разбиваем URL на 2 части: до знака ? и после
		switch($whot) {
			case "url":
				$url_part = str_replace(" ", "%20", $url_part); // заменим пробел на сущность
				return $url_part; // Возвращаем URL без get-параметров (до знака вопроса)
			break;
			case "get_params":
				return $get_part; // Возвращаем get-параметры (без знака вопроса)
			break;
			default:
				return false;
		}
	}
}

/**
* @since 1.1.0
*
* @param string 		$option_name (require)
* @param any 			$value (require)
* @param string/bool 	$autoload (not require) (yes/no or true/false)
*
* @return true/false
* Returns what might be the result of a add_blog_option or add_option
*/
if (!function_exists('univ_option_add')) {
	function univ_option_add($option_name, $value, $autoload = 'no') {
		if (is_multisite()) { 
			return add_blog_option(get_current_blog_id(), $option_name, $value);
		} else {
			return add_option($option_name, $value, '', $autoload);
		}
	}
}

/**
* @since 1.1.0
*
* @param string 		$option_name (require)
* @param any 			$newvalue (require)
* @param string/bool 	$autoload (not require) (yes/no or true/false)
*
* @return true/false
* Returns what might be the result of a update_blog_option or update_option
*/
if (!function_exists('univ_option_upd')) {
	function univ_option_upd($option_name, $newvalue, $autoload = 'no') {
		if (is_multisite()) { 
			return update_blog_option(get_current_blog_id(), $option_name, $newvalue);
		} else {
			return update_option($option_name, $newvalue, $autoload);
		}
	}
}

/**
* @since 1.1.0
*
* @param string 		$option_name (require)
* @param any 			$default (not require) - value to return if the option does not exist
*
* @return true/false
* Returns what might be the result of a get_blog_option or get_option
*/
if (!function_exists('univ_option_get')) {
	function univ_option_get($option_name, $default = false) {
		if (is_multisite()) { 
			return get_blog_option(get_current_blog_id(), $option_name, $default);
		} else {
			return get_option($option_name, $default);
		}
	}
}

/**
* @since 1.1.0
*
* @param string 		$option_name (require)
*
* @return true/false
* Returns what might be the result of a delete_blog_option or delete_option
*/

if (!function_exists('univ_option_del')) {
	function univ_option_del($option_name) {
		if (is_multisite()) { 
			return delete_blog_option(get_current_blog_id(), $option_name);
		} else {
			return delete_option($option_name);
		}
	}
}
?>