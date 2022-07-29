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

		$brpv_stat_of = 'all';
		$brpv_get_type = array('post', 'page');
		$brpv_meta_key = 'brpv_pageviews';
		$brpv_posts_per_page = 50;
		$brpv_orderby = 'meta_value_num';
		$brpv_order = 'ASC'; 
		
		$args = array(	
			'post_type' => $brpv_get_type,
			'meta_key' => $brpv_meta_key,
			'posts_per_page' => $brpv_posts_per_page,
			'orderby' => $brpv_orderby,
			'order' => $brpv_order, 	
			'get_status' => 'publish',
		);

		$brpv = new WP_Query($args); 
		if ($brpv->have_posts()) {while($brpv->have_posts()) {
			$brpv->the_post(); $post_id = get_the_ID();
			// get_the_permalink();
			if (get_post_meta($post_id, 'brpv_lastime', true) !== '') {
				$unixDate = (int)get_post_meta($post_id, 'brpv_lastime', true); 
				$normalDate = date('d/m/Y g:i A', $unixDate);			
			} else {
				$normalDate = '';
			}
			$result_arr[] = array(
				'brpv_title' 				=> get_the_title(),
				'brpv_rating' 				=> get_post_meta($post_id, 'brpv_total_rating', true),
				'brpv_votes' 				=> get_post_meta($post_id, 'brpv_golosov', true),
				'brpv_page_views' 			=> get_post_meta($post_id, 'brpv_pageviews', true),
				'brpv_date_of_last_visit'	=> $normalDate
			);
		}}
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
		// пагинация 
		$per_page = 3;
		$current_page = $this->get_pagenum();
		$total_items = count($this->table_data());
		$found_data = array_slice($this->table_data(), (($current_page - 1) * $per_page), $per_page);
		$this->set_pagination_args(array(
			'total_items' => $total_items, // Мы должны вычислить общее количество элементов
			'per_page'	  => $per_page // Мы должны определить, сколько элементов отображается на странице
		));
		// end пагинация 
		$this->items = $found_data; // $this->items = $this->table_data() // Получаем данные для формирования таблицы
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
	// Флажки для строк должны быть определены отдельно. Как упоминалось выше, есть метод column_{column} для отображения столбца. cb-столбец – особый случай:
/*	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="checkbox_xml_file[]" value="%s" />', $item['brpv_title']
		);
	}*/
}
?>