<?php if (!defined('ABSPATH')) {exit;}
function brpv_settings_page() {
 if (isset($_REQUEST['brpv_submit_action'])) {
  if (!empty($_POST) && check_admin_referer('brpv_nonce_action', 'brpv_nonce_field')) {
	if (is_multisite()) {
		if (isset($_POST['brpv_submit_action'])) {
			update_blog_option(get_current_blog_id(), 'brpv_not_count_bots', sanitize_text_field($_POST['brpv_not_count_bots']));
		}
		if (isset($_POST['brpv_rating_icons'])) {
			update_blog_option(get_current_blog_id(), 'brpv_rating_icons', sanitize_text_field($_POST['brpv_rating_icons']));
		}
	} else {
		if (isset($_POST['brpv_submit_action'])) {
			update_option('brpv_not_count_bots', sanitize_text_field($_POST['brpv_not_count_bots']));
		}
		if (isset($_POST['brpv_rating_icons'])) {
			update_option('brpv_rating_icons', sanitize_text_field($_POST['brpv_rating_icons']));
		}		
	}
  }
 } 
 if (is_multisite()) {
	$not_count_bots = get_blog_option(get_current_blog_id(), 'brpv_not_count_bots');
	$brpv_rating_icons = get_blog_option(get_current_blog_id(), 'brpv_rating_icons');
 } else {
	$not_count_bots = get_option('brpv_not_count_bots');
	$brpv_rating_icons = get_option('brpv_rating_icons');
 }
?>
 <div class="wrap">
  <h1><?php _e('Best Rating & Pageviews Settings', 'brpv'); ?></h1>
  <div style="margin: 0 auto; max-width: 1332px" class="clear">
  <div class="icp_wrap">
	<input type="radio" name="icp_slides" id="icp_point1">
	<input type="radio" name="icp_slides" id="icp_point2">
	<input type="radio" name="icp_slides" id="icp_point3" checked>	
	<div class="icp_slider">
		<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
		<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
		<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
	</div>	
	<div class="icp_control">
		<label for="icp_point1"></label>
		<label for="icp_point2"></label>
		<label for="icp_point3"></label>
	</div>
  </div>  	
  </div>  
  <div id="dashboard-widgets-wrap"><div id="dashboard-widgets" class="metabox-holder">	
	<div id="postbox-container-1" class="postbox-container"><div class="meta-box-sortables" >
     <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">	
	 <div class="postbox">
	   <div class="inside">
	    <h1><?php _e('Main parameters', 'brpv'); ?></h1>
		<table class="form-table"><tbody>
		 <tr>		
			<th scope="row"><label for="brpv_not_count_bots"><?php _e('Not count bots', 'brpv'); ?></label></th>
			<td class="overalldesc">
				<select name="brpv_not_count_bots">					
					<option value="yes" <?php selected($not_count_bots, 'yes'); ?>><?php _e('Yes', 'brpv'); ?></option>
					<option value="no" <?php selected($not_count_bots, 'no'); ?>><?php _e('No', 'brpv'); ?></option>
				</select><br />
				<span class="description"><?php _e('Do not count the bots visiting the site', 'brpv'); ?></span>
			</td>
		 </tr>	
		 <tr>		
			<th scope="row"><label for="brpv_rating_icons"><?php _e('Rating icons', 'brpv'); ?></label></th>
			<td class="overalldesc">
				<input class="brpv_radio" type="radio" name="brpv_rating_icons" value="brpv_pic1" <?php checked($brpv_rating_icons, 'brpv_pic1'); ?>> <img src="<?php echo brpv_URL.'img/ratings1.png'; ?>" alt="" /><br />
				<input class="brpv_radio" type="radio" name="brpv_rating_icons" value="brpv_pic2" <?php checked($brpv_rating_icons, 'brpv_pic2'); ?>> <img src="<?php echo brpv_URL.'img/ratings2.png'; ?>" alt="" /><br />
				<input class="brpv_radio" type="radio" name="brpv_rating_icons" value="brpv_pic3" <?php checked($brpv_rating_icons, 'brpv_pic3'); ?>> <img src="<?php echo brpv_URL.'img/ratings3.png'; ?>" alt="" /><br />
				<span class="description"><?php _e('Rating icons', 'brpv'); ?></span>
			</td>
		 </tr>		 
		</tbody></table>
	  </div>
	 </div>
	 
	 <div class="postbox">
	 <div class="inside">
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"><?php wp_nonce_field('brpv_nonce_action','brpv_nonce_field'); ?><input class="button-primary" type="submit" name="brpv_submit_action" value="<?php _e('Save', 'brpv'); ?>" /><br />
			<span class="description"><?php _e('Click to save the settings', 'brpv'); ?></span></td>
		 </tr>
		</tbody></table>
	  </div>
	 </div>
	 </form>
	</div></div>
	
	<div id="postbox-container-2" class="postbox-container"><div class="meta-box-sortables" >    
	 <div class="postbox">
	  <div class="inside">
	  <h1><?php _e('Please support the project!', 'brpv'); ?></h1>
	  <p><?php _e('Thank you for using the plugin', 'brpv'); ?> <strong>Best Rating & Pageviews</strong></p>
	  <p><?php _e('Please help make the plugin better', 'brpv'); ?> <a href="//docs.google.com/forms/d/1Nv25ESNvJtUA0i3MQRmFy9pvK1qgEUgq-IJzIcGfvp4" target="_blank" ><?php _e('answering 5 questions', 'brpv'); ?>!</a></p>
	  <p><?php _e('If this plugin useful to you, please support the project one way', 'brpv'); ?>:</p>
	  <ul>
		<li>- <a href="//wordpress.org/plugins/best-rating-pageviews/" target="_blank"><?php _e('Leave a comment on the plugin page', 'brpv'); ?></a>.</li>
		<li>- <?php _e('Support the project financially. Even $1 is a help!', 'brpv'); ?><a href="https://icopydoc.ru/donate/" target="_blank"> <?php _e('Donate now', 'brpv'); ?></a>.</li>
		<li>- <?php _e('Noticed a bug or have an idea how to improve the quality of the plugin?', 'brpv'); ?> <a href="mailto:pt070@yandex.ru"><?php _e('Let me know', 'brpv'); ?></a>.</li>
	  </ul>
	  <p><?php _e('The author of the plugin Maxim Glazunov', 'brpv'); ?>.</p>
	  <p><span style="color: red;"><?php _e('Accept orders for individual revision of the plugin', 'brpv'); ?></span>:<br /><a href="mailto:pt070@yandex.ru"><?php _e('Leave a request', 'brpv'); ?></a>.</p>  
	  </p>
	  </div>
	 </div>
	 
	 <div class="postbox">
	  <div class="inside">
		<h1><?php _e('Examples shotcodes', 'brpv'); ?>:</h1>		
		<p><strong><?php _e('To display the rating stars', 'brpv'); ?></strong></p>
		<p><strong>[pageratings]</strong><br /><?php _e('or in php code', 'brpv'); ?>:<br /> 
		<strong>do_shortcode('[pageratings]');</strong></p>		
		<p><strong><?php _e('To display the number of page views', 'brpv'); ?></strong></p>
		<p><strong>[pageviews]</strong><br /><?php _e('or in php code', 'brpv'); ?>:<br />
		<strong>do_shortcode('[pageviews]');</strong></p>
		<?php _e('Notice', 'brpv'); ?>:<br />
		- <?php _e('This shortcodes can be used in the body of the article', 'brpv'); ?>.<br />
		- <?php _e('This shortcodes can be used in the loop body of templates', 'brpv'); ?>.<br />
		- <?php _e('This shortcodes cannot be used outside the loop of the template', 'brpv'); ?>.
	  </div>
	 </div>
	</div></div>
	
  </div></div>
 </div>
 <style>.brpv_radio {margin-bottom: 35px !important; height: 13px !important;}</style>
<?php
} /* end функция настроек */ 
?>