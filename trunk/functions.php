<?php if (!defined('ABSPATH')) {exit;}
/*
* @since 2.0.0
*
* @param string $option_name (require)
* @param string $value (require)
* @param string $n (not require)
* @param string $autoload (not require) (yes/no)
* @param string $type (not require)
* @param string $source_settings_name (not require)
*
* @return true/false
* Возвращает то, что может быть результатом add_blog_option, add_option
*/
function brpv_optionADD($option_name, $value = '', $n = '', $autoload = 'yes', $type = 'option', $source_settings_name = '') {
	if ($option_name == '') {return false;}
	switch ($type) {
		case "set_arr":
			if ($n === '') {$n = '1';}
			$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
			$brpv_settings_arr[$n][$option_name] = $value;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), 'brpv_settings_arr', $brpv_settings_arr);
			} else {
				return update_option('brpv_settings_arr', $brpv_settings_arr, $autoload);
			}
		break;
		case "custom_set_arr":
			if ($source_settings_name === '') {return false;}
			if ($n === '') {$n = '1';}
			$brpv_settings_arr = brpv_optionGET($source_settings_name);
			$brpv_settings_arr[$n][$option_name] = $value;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), $source_settings_name, $brpv_settings_arr);
			} else {
				return update_option($source_settings_name, $brpv_settings_arr, $autoload);
			}
		break;
		default:
			if ($n === '1') {$n = '';}
			$option_name = $option_name.$n;
			if (is_multisite()) { 
				return add_blog_option(get_current_blog_id(), $option_name, $value);
			} else {
				return add_option($option_name, $value, '', $autoload);
			}
	}
}
/*
* @since 2.0.0
*
* @param string $option_name (require)
* @param string $value (not require)
* @param string $n (not require)
* @param string $autoload (not require) (yes/no)
* @param string $type (not require)
* @param string $source_settings_name (not require)
*
* @return true/false
* Возвращает то, что может быть результатом update_blog_option, update_option
*/
function brpv_optionUPD($option_name, $value = '', $n = '', $autoload = 'yes', $type = '', $source_settings_name = '') {
	if ($option_name == '') {return false;}
	switch ($type) {
		case "set_arr": 
			if ($n === '') {$n = '1';}
			$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
			$brpv_settings_arr[$n][$option_name] = $value;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), 'brpv_settings_arr', $brpv_settings_arr);
			} else {
				return update_option('brpv_settings_arr', $brpv_settings_arr, $autoload);
			}
		break;
		case "custom_set_arr": 
			if ($source_settings_name === '') {return false;}
			if ($n === '') {$n = '1';}
			$brpv_settings_arr = brpv_optionGET($source_settings_name);
			$brpv_settings_arr[$n][$option_name] = $value;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), $source_settings_name, $brpv_settings_arr);
			} else {
				return update_option($source_settings_name, $brpv_settings_arr, $autoload);
			}
		break;
		default:
			if ($n === '1') {$n = '';}
			$option_name = $option_name.$n;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), $option_name, $value);
			} else {
				return update_option($option_name, $value, $autoload);
			}
	}
}
/*
* @since 2.0.0
*
* @param string $option_name (require)
* @param string $n (not require)
* @param string $type (not require)
* @param string $source_settings_name (not require)
*
* @return Значение опции или false
* Возвращает то, что может быть результатом get_blog_option, get_option
*/
function brpv_optionGET($option_name, $n = '', $type = '', $source_settings_name = '') {
	if ($option_name == '') {return false;}	
	switch ($type) {
		case "set_arr": 
			if ($n === '') {$n = '1';}
			$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
			if (isset($brpv_settings_arr[$n][$option_name])) {
				return $brpv_settings_arr[$n][$option_name];
			} else {
				return false;
			}
		break;
		case "custom_set_arr":
			if ($source_settings_name === '') {return false;}
			if ($n === '') {$n = '1';}
			$brpv_settings_arr = brpv_optionGET($source_settings_name);
			if (isset($brpv_settings_arr[$n][$option_name])) {
				return $brpv_settings_arr[$n][$option_name];
			} else {
				return false;
			}
		break;
		case "for_update_option":
			if ($n === '1') {$n = '';}
			$option_name = $option_name.$n;
			if (is_multisite()) { 
				return get_blog_option(get_current_blog_id(), $option_name);
			} else {
				return get_option($option_name);
			}		
		break;
		default:
			if ($n === '1') {$n = '';}
			$option_name = $option_name.$n;
			if (is_multisite()) { 
				return get_blog_option(get_current_blog_id(), $option_name);
			} else {
				return get_option($option_name);
			}
	}
}
/*
* @since 2.0.0
*
* @param string $option_name (require)
* @param string $n (not require)
* @param string $type (not require)
* @param string $source_settings_name (not require)
*
* @return true/false
* Возвращает то, что может быть результатом delete_blog_option, delete_option
*/
function brpv_optionDEL($option_name, $n = '', $type = '', $source_settings_name = '') {
	if ($option_name == '') {return false;}	 
	switch ($type) {
		case "set_arr": 
			if ($n === '') {$n = '1';} 
			$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
			unset($brpv_settings_arr[$n][$option_name]);
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), 'brpv_settings_arr', $brpv_settings_arr);
			} else {
				return update_option('brpv_settings_arr', $brpv_settings_arr);
			}
		break;
		case "custom_set_arr": 
			if ($source_settings_name === '') {return false;}
			if ($n === '') {$n = '1';} 
			$brpv_settings_arr = brpv_optionGET($source_settings_name);
			unset($brpv_settings_arr[$n][$option_name]);
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), $source_settings_name, $brpv_settings_arr);
			} else {
				return update_option($source_settings_name, $brpv_settings_arr);
			}
		break;
		default:
		if ($n === '1') {$n = '';} 
		$option_name = $option_name.$n;
		if (is_multisite()) { 
			return delete_blog_option(get_current_blog_id(), $option_name);
		} else {
			return delete_option($option_name);
		}
	}
} 
?>