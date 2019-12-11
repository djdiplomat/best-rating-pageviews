<?php if ( ! defined('ABSPATH') ) { exit; }
function brpv_statistics_page() { 
 
 $brpv_stat_of = 'all';
 $brpv_get_type = array('post', 'page');
 $brpv_meta_key = 'brpv_pageviews';
 $brpv_posts_per_page = 20;
 $brpv_orderby = 'meta_value_num';
 $brpv_order = 'ASC'; 	

 if (isset($_REQUEST['brpv_show_action'])) {
  if (!empty($_POST) && check_admin_referer('brpv_nonce_action', 'brpv_nonce_field')) {
	if (isset($_POST['brpv_stat_of']) and ($_POST['brpv_stat_of'] !== '')) {
		$brpv_stat_of = sanitize_text_field($_POST['brpv_stat_of']);
		if ($brpv_stat_of !== 'all') {$brpv_get_type = array($brpv_stat_of);}
	}
	if (isset($_POST['brpv_meta_key']) and ($_POST['brpv_meta_key'] !== '')) {
		$brpv_meta_key = sanitize_text_field($_POST['brpv_meta_key']);
	}
	if (isset($_POST['brpv_order']) and ($_POST['brpv_order'] !== '')) {
		$brpv_order = sanitize_text_field($_POST['brpv_order']);
	} 
	if (isset($_POST['brpv_posts_per_page']) and ($_POST['brpv_posts_per_page'] !== '')) {
		$brpv_posts_per_page = (int)sanitize_text_field($_POST['brpv_posts_per_page']);
	}
  }
 
 }
 
 $args = array(	
	'post_type' => $brpv_get_type,
	'meta_key' => $brpv_meta_key,
	'posts_per_page' => $brpv_posts_per_page,
	'orderby' => $brpv_orderby,
	'order' => $brpv_order, 	
	'get_status' => 'publish',
 );
?>
<div id="welcome-panel" class="wrap">
  <h1><?php _e('Statistics', 'brpv'); ?></h1>
  <div class="welcome-panel">
	<div class="welcome-panel-content">
	 <h2><?php _e('Consolidated report', 'brpv'); ?></h2><br />
	 <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	  <?php _e('Statistics by', 'brpv'); ?>:	  
	  <select name="brpv_stat_of">					
		<option value="post" <?php selected($brpv_stat_of, 'post'); ?>><?php _e('Posts', 'brpv'); ?></option>
		<option value="page" <?php selected($brpv_stat_of, 'page'); ?>><?php _e('Pages', 'brpv'); ?></option>
		<option value="all" <?php selected($brpv_stat_of, 'all'); ?>><?php _e('Posts & Pages', 'brpv'); ?></option>					
	 </select>
	 <?php _e('Order', 'brpv'); ?>:
	 <select name="brpv_order">					
		<option value="ASC" <?php selected($brpv_order, 'ASC'); ?>><?php _e('ASC', 'brpv'); ?></option>
		<option value="DESC" <?php selected($brpv_order, 'DESC'); ?>><?php _e('DESC', 'brpv'); ?></option>
	 </select>
	 <select name="brpv_meta_key">					
		<option value="brpv_total_rating" <?php selected($brpv_meta_key, 'brpv_total_rating'); ?>><?php _e('Order by Rating', 'brpv'); ?></option>
		<option value="brpv_golosov" <?php selected($brpv_meta_key, 'brpv_golosov'); ?>><?php _e('Order by Votes', 'brpv'); ?></option>
		<option value="brpv_pageviews" <?php selected($brpv_meta_key, 'brpv_pageviews'); ?>><?php _e('Order by Page views', 'brpv'); ?></option>
		<option value="brpv_lastime" <?php selected($brpv_meta_key, 'brpv_lastime'); ?>><?php _e('Order by date of last visit', 'brpv'); ?></option>					
	 </select>
	 <input type="number" min="1" max="200" step="1" name="brpv_posts_per_page" value="<?php echo $brpv_posts_per_page; ?>" />
	 <?php wp_nonce_field('brpv_nonce_action','brpv_nonce_field'); ?><input class="button" type="submit" name="brpv_show_action" value="<?php _e('Show', 'brpv'); ?>" />	 
	 </form>
	 <table id="brpv" class="wp-list-table widefat fixed striped pages">	
	  <thead>
		<tr>
		 <th colspan="4" class="column-title"><?php _e('Title', 'brpv'); ?></th>
		 <th class="column-title"><?php _e('Rating', 'brpv'); ?></th>
		 <th class="column-title"><?php _e('Votes', 'brpv'); ?></th>
		 <th class="column-title"><?php _e('Page Views', 'brpv'); ?></th>
		 <th class="column-title"><?php _e('Date of last visit', 'brpv'); ?></th>
		</tr>
	  </thead>
	  <tbody>
		<?php 
		 $brpv = new WP_Query($args); 
		 if ($brpv->have_posts()) : while($brpv->have_posts()) : 
			$brpv->the_post(); $postId = get_the_ID(); ?>
			<tr>
			 <td colspan="4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<div class="row-actions"><span class="edit">
					<a href="/wp-admin/post.php?post=<?php echo $postId ?>&action=edit"><?php _e('Edit', 'brpv'); ?></a>
				</span></div>				
			 </td>
			 <td><?php echo get_post_meta($postId, 'brpv_total_rating', true); ?></td>
			 <td><?php echo get_post_meta($postId, 'brpv_golosov', true); ?></td>
			 <td><?php echo get_post_meta($postId, 'brpv_pageviews', true); ?></td>
			 <td><?php 
			 if (get_post_meta($postId, 'brpv_lastime', true) !== '') {
				$unixDate = (int)get_post_meta($postId, 'brpv_lastime', true); 
				$normalDate = date('d/m/Y g:i A', $unixDate);
				echo $normalDate;
			 } ?></td>			
			</tr>
		 <?php endwhile; endif; wp_reset_postdata(); ?>
	  </tbody>
	 </table><br />
	 <p class="about-description"><?php _e('Please note that there are no pages for which data was not collected in the results', 'brpv'); ?>!</p><br />	
	</div>
  </div>
</div>
<?php
} /* end функция настроек brpv_statistics_page */
?>