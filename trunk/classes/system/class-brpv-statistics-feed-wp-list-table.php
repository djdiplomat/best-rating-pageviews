<?php // https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html https://wp-kama.ru/function/wp_list_table
class BRPV_Statistics_WP_List_Table extends WP_List_Table {
	function __construct() {
		global $status, $page;
		parent::__construct( array(
			'plural'	=> '', 		// По умолчанию: '' ($this->screen->base); Название для множественного числа, используется во всяких заголовках, например в css классах, в заметках, например 'posts', тогда 'posts' будет добавлен в класс table.
			'singular'	=> '', 		// По умолчанию: ''; Название для единственного числа, например 'post'. 
			'ajax'		=> false,	// По умолчанию: false; Должна ли поддерживать таблица AJAX. Если true, класс будет вызывать метод _js_vars() в подвале, чтобы передать нужные переменные любому скрипту обрабатывающему AJAX события.
			'screen'	=> null, 	// По умолчанию: null; Строка содержащая название хука, нужного для определения текущей страницы. Если null, то будет установлен текущий экран.
		) );
		add_action('admin_footer', array($this, 'admin_header')); // меняем ширину колонок	
	}

	/*	Сейчас у таблицы стандартные стили WordPress. Чтобы это исправить, вам нужно адаптировать классы CSS, которые были 
	*	автоматически применены к каждому столбцу. Название класса состоит из строки «column-» и ключевого имени 
	* 	массива $columns, например «column-isbn» или «column-author».
	*	В качестве примера мы переопределим ширину столбцов (для простоты, стили прописаны непосредственно в HTML разделе head)
	*/
	function admin_header() {
/*		echo '<style type="text/css">'; 
		echo '#brpv_title, .column-brpv_title {width: 7%;}';
		echo '</style>';*/
	}

	/*	Метод get_columns() необходим для маркировки столбцов внизу и вверху таблицы. 
	*	Ключи в массиве должны быть теми же, что и в массиве данных, 
	*	иначе соответствующие столбцы не будут отображены.
	*/
	function get_columns() {
		$columns = array(
//			'cb'							=> '<input type="checkbox" />', // флажок сортировки. см get_bulk_actions и column_cb
			'brpv_title'				=> __('Title', 'brpv'),
			'brpv_rating'				=> __('Rating', 'brpv'),
			'brpv_votes'				=> __('Votes', 'brpv'),
			'brpv_page_views'			=> __('Page Views', 'brpv'),
			'brpv_date_of_last_visit'	=> __('Date of last visit', 'brpv'),
		);
		return $columns;
	}
	/*	
	*	Метод вытаскивает из БД данные, которые будут лежать в таблице
	*	$this->table_data();
	*/
	private function table_data() {
		$result_arr = array();

		if (is_multisite()) {
			$brpv_get_type_arr = get_blog_option(get_current_blog_id(), 'brpv_posts_type_arr');
		} else {
			$brpv_get_type_arr = get_option('brpv_posts_type_arr');
		}

		/*
		$brpv_get_type_arr = array('post', 'page');		
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins', array()))) && !(is_multisite() && array_key_exists($plugin, get_site_option('active_sitewide_plugins', array())))) {
			$brpv_get_type_arr[] = 'product';
		} */

		$args = array(	
			'post_type' => $brpv_get_type_arr,
			'fields' => 'ids',
			'posts_per_page' => -1,
			'get_status' => 'publish',
		);

		$featured_query = new WP_Query($args);
		if ($featured_query->have_posts()) { 
			for ($i = 0; $i < count($featured_query->posts); $i++) {
				$cur_post_id = $featured_query->posts[$i];
				if (get_post_meta($cur_post_id, 'brpv_lastime', true) !== '') {
					$unix_date = (int)get_post_meta($cur_post_id, 'brpv_lastime', true); 
					$normal_date = date('d/m/Y g:i A', $unix_date);			
				} else {
					$normal_date = '';
				}
				$result_arr[] = array(
					'brpv_title' 				=> sprintf('<a href="%1$s">%2$s</a>', get_the_permalink($cur_post_id), get_the_title($cur_post_id)),
					'brpv_rating' 				=> (int)get_post_meta($cur_post_id, 'brpv_total_rating', true),
					'brpv_votes' 				=> (int)get_post_meta($cur_post_id, 'brpv_golosov', true),
					'brpv_page_views' 			=> (int)get_post_meta($cur_post_id, 'brpv_pageviews', true),
					'brpv_date_of_last_visit'	=> $normal_date
				);
			}
		}

		wp_reset_postdata();

		return $result_arr;
	}

	/*
	*	prepare_items определяет два массива, управляющие работой таблицы:
	*	$hidden определяет скрытые столбцы https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html#screen-options
	*	$sortable определяет, может ли таблица быть отсортирована по этому столбцу.
	*
	*/
	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns(); // вызов сортировки
		$this->_column_headers = array($columns, $hidden, $sortable);
		$table_data = $this->table_data(); // данные для формирования таблицы
		usort($table_data, array(&$this, 'usort_reorder')); // сортировка в usort_reorder() для работы get_sortable_columns()
		// пагинация 
		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = count($table_data);	
		$found_data = array_slice($table_data, (($current_page - 1) * $per_page), $per_page);
		$this->set_pagination_args(array(
			'total_items' => $total_items, // Мы должны вычислить общее количество элементов
			'per_page'	  => $per_page // Мы должны определить, сколько элементов отображается на странице
		));
		// end пагинация 
		$this->items = $found_data; // $this->items = $table_data // Получаем данные для формирования таблицы
	}
	/*
	* 	Данные таблицы.
	*	Наконец, метод назначает данные из примера на переменную представления данных класса — items.
	*	Прежде чем отобразить каждый столбец, WordPress ищет методы типа column_{key_name}, например, function column_brpv_rating. 
	*	Такой метод должен быть указан для каждого столбца. Но чтобы не создавать эти методы для всех столбцов в отдельности, 
	*	можно использовать column_default. Эта функция обработает все столбцы, для которых не определён специальный метод:
	*/ 
	function column_default($item, $column_name) {
		switch($column_name) {
			case 'brpv_title':
			case 'brpv_rating':
			case 'brpv_votes':
			case 'brpv_page_views':
			case 'brpv_date_of_last_visit':
				return $item[$column_name];
			default:
				return print_r($item, true) ; // Мы отображаем целый массив во избежание проблем
		}
	}

	/*
	* 	Функция сортировки.
	*	Второй параметр в массиве значений $sortable_columns отвечает за порядок сортировки столбца. 
	*	Если значение true, столбец будет сортироваться в порядке возрастания, если значение false, столбец сортируется в порядке 
	*	убывания, или не упорядочивается. Это необходимо для маленького треугольника около названия столбца, который указывает порядок
	*	сортировки, чтобы строки отображались в правильном направлении
	*/
	function get_sortable_columns() {
		$sortable_columns = array(
			'brpv_title'		=> array('brpv_title', true),
			'brpv_rating'		=> array('brpv_rating', true),
			'brpv_votes'		=> array('brpv_votes', true),
			'brpv_page_views'	=> array('brpv_page_views', false)
		);
		return $sortable_columns;
	}

	function usort_reorder($a, $b) {
		// Если не отсортировано, по умолчанию brpv_title
		if (!empty( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
		} else {
			$orderby = 'brpv_title';
		} 
		// Если не отсортировано, по умолчанию asc
		if (!empty( $_GET['order'] ) ) {
			$order = $_GET['order'];
		} else {
			$order = 'asc';
		} 

		// https://phpstack.ru/php/sortirovka-massivov-v-php-ksort-asort-i-procie-sorty.html
		if ($order === 'asc') {
			$result = ($a[$orderby] > $b[$orderby]);
		} else {
			$result = ($a[$orderby] < $b[$orderby]);
		}

		return $result;
	}

	// Флажки для строк должны быть определены отдельно. Как упоминалось выше, есть метод column_{column} для отображения столбца. cb-столбец – особый случай:
	/* function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="checkbox_xml_file[]" value="%s" />', $item['brpv_title']
		);
	} */
	/*
	* Нет элементов.
	* Если в списке нет никаких элементов, отображается стандартное сообщение «No items found.». Если вы хотите изменить это сообщение, вы можете переписать метод no_items():
	*/
	function no_items() {
		_e('No data availabled', 'brpv');
	}
}
?>