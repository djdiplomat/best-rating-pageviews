<?php if (!defined('ABSPATH')) {exit;}
function brpv_settings_page() { 
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
  <?php do_action('brpv_before_poststuff'); ?>
  <div id="poststuff"><div id="post-body" class="columns-2">
   <div id="postbox-container-1" class="postbox-container"><div class="meta-box-sortables">
  	<?php do_action('brpv_prepend_container_1'); ?>
	<div class="postbox">
	 <h2 class="hndle"><?php _e('Clear all statistics', 'brpv'); ?>!</h2>
	  <div class="inside">
	  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	  	<?php wp_nonce_field('brpv_nonce_action_clear_stat', 'brpv_nonce_clear_stat_field'); ?>
	  	<input id="brpv_submit_clear_stat" class="button" type="submit" name="brpv_submit_clear_stat" value="<?php _e('Clear statistics', 'brpv'); ?>" />
	  </form>
	  </div>
	</div>
	<?php do_action('brpv_between_container_1'); ?>
	<div class="postbox">
	 <h2 class="hndle"><?php _e('Please support the project', 'brpv'); ?>!</h2>
	 <div class="inside">	  
		<p><?php _e('Thank you for using the plugin', 'brpv'); ?> <strong>Best Rating & Pageviews</strong></p>
		<p><?php _e('Please help make the plugin better', 'brpv'); ?> <a href="//docs.google.com/forms/d/1Nv25ESNvJtUA0i3MQRmFy9pvK1qgEUgq-IJzIcGfvp4" target="_blank" ><?php _e('answering 5 questions', 'brpv'); ?>!</a></p>
	  	<p><?php _e('If this plugin useful to you, please support the project one way', 'brpv'); ?>:</p>
		<ul class="brpv_ul">
			<li><a href="//wordpress.org/plugins/best-rating-pageviews/" target="_blank"><?php _e('Leave a comment on the plugin page', 'brpv'); ?></a>.</li>
			<li><?php _e('Support the project financially', 'brpv'); ?>. <a href="//sobe.ru/na/best_rating_pageviews" target="_blank"> <?php _e('Donate now', 'brpv'); ?></a>.</li>
			<li><?php _e('Noticed a bug or have an idea how to improve the quality of the plugin?', 'brpv'); ?> <a href="mailto:support@icopydoc.ru"><?php _e('Let me know', 'brpv'); ?></a>.</li>
	    </ul>
		<p><?php _e('The author of the plugin Maxim Glazunov', 'brpv'); ?>.</p>
		<p><span style="color: red;"><?php _e('Accept orders for individual revision of the plugin', 'brpv'); ?></span>:<br /><a href="mailto:support@icopydoc.ru"><?php _e('Leave a request', 'brpv'); ?></a>.</p>
	  </div>
	</div>
	<?php do_action('brpv_append_container_1'); ?>
  </div></div>

  <div id="postbox-container-2" class="postbox-container"><div class="meta-box-sortables">
  	<?php do_action('brpv_prepend_container_2'); ?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	 <?php do_action('brpv_prepend_form_container_2'); ?>
	 <div class="postbox">
	  <h2 class="hndle"><?php _e('Main parameters', 'brpv'); ?></h2>
	   <div class="inside">
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
		 <?php do_action('brpv_after_step_export'); ?>		 		 
		</tbody></table>
	   </div>
	 </div>	
	 <div class="postbox">
	 <h2 class="hndle"><?php _e('Examples shotcodes', 'brpv'); ?></h2>	
	  <div class="inside">	
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
	 <?php do_action('brpv_before_button_primary_submit'); ?>	 
	 <div class="postbox">
	  <div class="inside">
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"><?php wp_nonce_field('brpv_nonce_action', 'brpv_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="brpv_submit_action" value="<?php _e('Save', 'brpv'); ?>" /><br />
			<span class="description"><?php _e('Click to save the settings', 'brpv'); ?></span></td>
		 </tr>
		</tbody></table>
	  </div>
	 </div>
	 <?php do_action('brpv_append_form_container_2'); ?>
	</form>
	<?php do_action('brpv_append_container_2'); ?>
  </div></div>
 </div><!-- /post-body --><br class="clear"></div><!-- /poststuff -->
 <?php do_action('brpv_after_poststuff'); ?>

 <div id="icp_slides" class="clear">
  <div class="icp_wrap">
	<input type="radio" name="icp_slides" id="icp_point1">
	<input type="radio" name="icp_slides" id="icp_point2">
	<input type="radio" name="icp_slides" id="icp_point3" checked>
	<input type="radio" name="icp_slides" id="icp_point4">
	<input type="radio" name="icp_slides" id="icp_point5">
	<input type="radio" name="icp_slides" id="icp_point6">
	<input type="radio" name="icp_slides" id="icp_point7">
	<div class="icp_slider">
		<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
		<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
		<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
		<div class="icp_slides icp_img4"><a href="//wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"></a></div>
		<div class="icp_slides icp_img5"><a href="//wordpress.org/plugins/xml-for-avito/" target="_blank"></a></div>
		<div class="icp_slides icp_img6"><a href="//wordpress.org/plugins/xml-for-o-yandex/" target="_blank"></a></div>
		<div class="icp_slides icp_img7"><a href="//wordpress.org/plugins/import-from-yml/" target="_blank"></a></div>
	</div>
	<div class="icp_control">
		<label for="icp_point1"></label>
		<label for="icp_point2"></label>
		<label for="icp_point3"></label>
		<label for="icp_point4"></label>
		<label for="icp_point5"></label>
		<label for="icp_point6"></label>
		<label for="icp_point7"></label>
	</div>
  </div> 
 </div>
 <?php do_action('brpv_after_icp_slides'); ?>

 <div class="metabox-holder">
  <div class="postbox">
  	<h2 class="hndle"><?php _e('My plugins that may interest you', 'brpv'); ?></h2>
	  <div class="inside">
		<p><span class="brpv_bold">XML for Google Merchant Center</span> - <?php _e('Сreates a XML-feed to upload to Google Merchant Center', 'brpv'); ?>. <a href="https://wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p> 
		<p><span class="brpv_bold">YML for Yandex Market</span> - <?php _e('Сreates a YML-feed for importing your products to Yandex Market', 'brpv'); ?>. <a href="https://wordpress.org/plugins/yml-for-yandex-market/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
		<p><span class="brpv_bold">Import from YML</span> - <?php _e('Imports products from YML to your shop', 'brpv'); ?>. <a href="https://wordpress.org/plugins/import-from-yml/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
		<p><span class="brpv_bold">XML for Hotline</span> - <?php _e('Сreates a XML-feed for importing your products to Hotline', 'brpv'); ?>. <a href="https://wordpress.org/plugins/xml-for-hotline/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
		<p><span class="brpv_bold">Gift upon purchase for WooCommerce</span> - <?php _e('This plugin will add a marketing tool that will allow you to give gifts to the buyer upon purchase', 'brpv'); ?>. <a href="https://wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
		<p><span class="brpv_bold">Import products to ok.ru</span> - <?php _e('With this plugin, you can import products to your group on ok.ru', 'brpv'); ?>. <a href="https://wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
		<p><span class="brpv_bold">XML for Avito</span> - <?php _e('Сreates a XML-feed for importing your products to', 'brpv'); ?> Avito. <a href="https://wordpress.org/plugins/xml-for-avito/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
		<p><span class="brpv_bold">XML for O.Yandex (Яндекс Объявления)</span> - <?php _e('Сreates a XML-feed for importing your products to', 'brpv'); ?> Яндекс.Объявления. <a href="https://wordpress.org/plugins/xml-for-o-yandex/" target="_blank"><?php _e('Read more', 'brpv'); ?></a>.</p>
	</div>
  </div>
 </div>
 <?php do_action('brpv_append_wrap'); ?>
</div><!-- /wrap -->
<?php
} /* end функция настроек */ 
?>