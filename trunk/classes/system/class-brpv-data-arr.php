<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Updates
*
* @link			https://icopydoc.ru/
* @since		2.1.0
*/

class BRPV_Data_Arr {
	private $data_arr = [
		array('brpv_status_sborki', '-1', 'private'),
		array('brpv_date_sborki', '0000000001', 'private'), // дата начала сборки
		array('brpv_date_sborki_end', '0000000001', 'private'), // дата завершения сборки
		array('brpv_date_save_set', '0000000001', 'private'), // дата сохранения настроек плагина
		array('brpv_count_products_in_feed', '-1', 'private'), // число товаров, попавших в фид
		array('brpv_file_url', '', 'private'),
		array('brpv_file_file', '', 'private'),
		array('brpv_errors', '', 'private'),
		array('brpv_status_cron', 'off', 'private'),

		array('brpv_step_export', '500', 'public'),
		array('brpv_type_sborki', 'yml', 'public'), // тип собираемого файла yml или xls	
	];

	public function __construct($blog_title = '', $currency_id_xml = '', $data_arr = array()) {
		if (empty($blog_title)) {
			$blog_title = mb_strimwidth(get_bloginfo('name'), 0, 20);
			$this->blog_title = $blog_title;
		}
		if (empty($currency_id_xml)) {
			if (class_exists('WooCommerce')) {$currency_id_xml = get_woocommerce_currency();} else {$currency_id_xml = 'USD';}
			$this->currency_id_xml = $currency_id_xml;
		}
		if (!empty($data_arr)) {
			$this->data_arr = $data_arr;
		}
		
		array_push($this->data_arr,
			array('brpv_shop_name', $this->blog_title, 'public'),
			array('brpv_company_name', $this->blog_title, 'public')
		);

		$args_arr = array($this->blog_title, $this->currency_id_xml);
		$this->data_arr = apply_filters('brpv_set_default_feed_settings_result_arr_filter', $this->data_arr, $args_arr);
	}

	public function get_data_arr() {
		return $this->data_arr;
	}

	// @return array([0] => opt_key1, [1] => opt_key2, ...)
	public function get_opts_name($whot = '') {
		if ($this->data_arr) {
			$res_arr = array();		
			for ($i = 0; $i < count($this->data_arr); $i++) {
				switch ($whot) {
					case "public":
						if ($this->data_arr[$i][2] === 'public') {
							$res_arr[] = $this->data_arr[$i][0];
						}
					break;
					case "private":
						if ($this->data_arr[$i][2] === 'private') {
							$res_arr[] = $this->data_arr[$i][0];
						}
					break;
					default:
						$res_arr[] = $this->data_arr[$i][0];
				}
			}
			return $res_arr;
		} else {
			return array();
		}
	}

	// @return array(opt_name1 => opt_val1, opt_name2 => opt_val2, ...)
	public function get_opts_name_and_def_date($whot = 'all') {
		if ($this->data_arr) {
			$res_arr = array();		
			for ($i = 0; $i < count($this->data_arr); $i++) {
				switch ($whot) {
					case "public":
						if ($this->data_arr[$i][2] === 'public') {
							$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
						}
					break;
					case "private":
						if ($this->data_arr[$i][2] === 'private') {
							$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
						}
					break;
					default:
						$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
				}
			}
			return $res_arr;
		} else {
			return array();
		}
	}

	public function get_opts_name_and_def_date_obj($whot = 'all') {		
		$source_arr = $this->get_opts_name_and_def_date($whot);

		$res_arr = array();	
		foreach($source_arr as $key => $value) {
			$res_arr[] = new BRPV_Data_Arr_Helper($key, $value); // return unit obj
		}
		return $res_arr;
	}
}
class BRPV_Data_Arr_Helper {	
	private $opt_name;
	private $opt_def_value;

	function __construct($name = '', $def_value = '') {
		$this->opt_name = $name;
		$this->opt_def_value = $def_value;
	}

	function get_name() {
		return $this->opt_name;
	}

	function get_value() {
		return $this->opt_def_value;
	}
}
?>