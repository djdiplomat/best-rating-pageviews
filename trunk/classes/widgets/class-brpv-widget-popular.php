<?php if (!defined('ABSPATH')) {exit;}
/**
* Widget Popular
*
* @link			https://icopydoc.ru/
* @since		2.1.0
*/
/* 
* Usage:
add_action('widgets_init', function () {
	register_widget('BRPV_Widget_Popular');
});
*/
class BRPV_Widget_Popular extends WP_Widget {
	public function __construct() {
		parent::__construct("text_widget",
			__('Popular', 'brpv'),
			array(
				'description' => __( 'Shows popular posts and pages based on the rating and pageviews', 'brpv'),		
			)
		);
	}

	//Метод form() (отвечает за внешний вид виджета в админке)
	public function form($instance) {
		$title = __('Popular', 'brpv'); // дефольный заголовок
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
		if ($brpv->have_posts()) : ?>
			<ul>
				<?php while($brpv->have_posts()):
					$brpv->the_post();
					$post_id = get_the_ID(); ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endwhile; ?>
			</ul>
		<?php endif;
		wp_reset_postdata(); // восстанавливаем глобальную переменную $post
		/* End Выводт виджета*/
		echo $args['after_widget']; // вывод обертки виджета (закрывающий тег)
	}
}
?>