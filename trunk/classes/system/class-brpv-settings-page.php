<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Settings Page
*
* @link			https://icopydoc.ru/
* @since		1.9.0
*/

class BRPV_Settings_Page {
	private $feed_id;
	private $feedback;

	public function __construct() {
		$this->feedback = new BRPV_Feedback();

		$this->init_hooks(); // подключим хуки
		$this->listen_submit();

		$this->get_html_form();	
	}

	public function get_html_form() { ?>
		<div class="wrap">
  			<h1><?php _e('Exporter', 'brpv'); ?> Best Rating & Pageviews</h1>
			<?php echo $this->get_html_banner(); ?>
			<div id="poststuff">
				<?php $this->get_html_feeds_list(); ?>

				<div id="post-body" class="columns-2">

					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_info_block(); ?>
							
							<?php do_action('brpv_before_support_project'); ?>

							<?php $this->feedback->get_block_support_project(); ?>

							<?php do_action('brpv_between_container_1', $this->get_feed_id()); ?>	

							<?php $this->feedback->get_form(); ?>

							<?php do_action('brpv_append_container_1', $this->get_feed_id()); ?>
						</div>
					</div><!-- /postbox-container-1 -->

					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables"><?php 
							if (isset($_GET['tab'])) {$tab = $_GET['tab'];} else {$tab = 'main_tab';}
							echo $this->get_html_tabs($tab); ?>

							<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
								<?php do_action('brpv_prepend_form_container_2', $this->get_feed_id()); ?>
								<input type="hidden" name="brpv_num_feed_for_save" value="<?php echo $this->get_feed_id(); ?>">
								<?php switch ($tab) : 
									case 'main_tab' : ?>
										<?php $this->get_html_main_settings(); ?>
										<?php break;
									case 'shop_data' : ?>
										<?php $this->get_html_shop_data(); ?>
										<?php break;
									case 'tags' : ?>
										<?php $this->get_html_tags_settings(); ?>
										<?php $brpv_settings_feed_wp_list_table = new BRPV_Settings_Feed_WP_List_Table($this->get_feed_id()); ?>
										<?php $brpv_settings_feed_wp_list_table->prepare_items(); $brpv_settings_feed_wp_list_table->display(); ?> 
										<?php do_action('brpv_before_button_primary_submit', $this->get_feed_id()); ?>
										<?php break;
									case 'filtration': ?>
										<?php $this->get_html_filtration(); ?>										
										<?php do_action('brpv_after_main_param_block', $this->get_feed_id()); ?>
								<?php break; ?>
								<?php endswitch; ?>

								<?php do_action('brpv_after_optional_elemet_block', $this->get_feed_id()); ?>
								<div class="postbox">
									<div class="inside">
										<table class="form-table"><tbody>
											<tr>
												<th scope="row"><label for="button-primary"></label></th>
												<td class="overalldesc"><?php wp_nonce_field('brpv_nonce_action', 'brpv_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="brpv_submit_action" value="<?php 
												if ($tab === 'main_tab') {
													echo __('Save', 'brpv').' & '. __('Create feed', 'brpv'); 
												} else {
													_e('Save', 'brpv');
												}
												?>"/><br />
												<span class="description"><small><?php _e('Click to save the settings', 'brpv'); ?><small></span></td>
											</tr>
										</tbody></table>
									</div>
								</div>
							</form>
						</div>
					</div><!-- /postbox-container-2 -->

				</div>
			</div><!-- /poststuff -->
			<?php $this->get_html_icp_banners(); ?>
			<?php $this->get_html_my_plugins_list(); ?>
		</div><?php // end get_html_form();
	}

	public function get_html_banner() {
		if (!class_exists('BestRatingPageviewsPro')) {
		return '<div class="notice notice-info">
			<p><span class="brpv_bold">Best Rating & Pageviews Pro</span> - '. __('a necessary extension for those who want to', 'brpv').' <span class="brpv_bold" style="color: green;">'. __('save on advertising budget', 'brpv').'</span> '. __('on Yandex', 'brpv').'! <a href="https://icopydoc.ru/product/yml-for-yandex-market-pro/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-xml-google-pro">'. __('Learn More', 'brpv').'</a>.</p> 
		</div>';
		} else {
			return '';
		}
	} // end get_html_banner();

	public function get_html_feeds_list() { 
		$brpvListTable = new BRPV_WP_List_Table(); ?>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field('brpv_nonce_action_add_new_feed', 'brpv_nonce_field_add_new_feed'); ?><input class="button" type="submit" name="brpv_submit_add_new_feed" value="<?php _e('Add New Feed', 'brpv'); ?>" />
		</form>
		<form method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<input type="hidden" name="brpv_form_id" value="brpv_wp_list_table" />
			<?php $brpvListTable->prepare_items(); $brpvListTable->display(); ?>
		</form><?php // end get_html_feeds_list();
	} // end get_html_feeds_list();

	public function get_html_info_block() { 
		$status_sborki = (int)brpv_optionGET('brpv_status_sborki', $this->get_feed_id());
		$brpv_file_url = urldecode(brpv_optionGET('brpv_file_url', $this->get_feed_id(), 'set_arr'));
		$brpv_date_sborki = brpv_optionGET('brpv_date_sborki', $this->get_feed_id(), 'set_arr');
		$brpv_date_sborki_end = brpv_optionGET('brpv_date_sborki_end', $this->get_feed_id(), 'set_arr');
		$brpv_status_cron = brpv_optionGET('brpv_status_cron', $this->get_feed_id(), 'set_arr'); 
		$brpv_count_products_in_feed = brpv_optionGET('brpv_count_products_in_feed', $this->get_feed_id(), 'set_arr');
		?>
		<div class="postbox">
			<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';} ?>
			<h2 class="hndle"><?php _e('Feed', 'brpv'); ?> <?php echo $this->get_feed_id(); ?>: <?php if ($this->get_feed_id() !== '1') {echo $this->get_feed_id();} ?>feed-yml-<?php echo $cur_blog_id; ?>.xml <?php $assignment = brpv_optionGET('brpv_feed_assignment', $this->get_feed_id(), 'set_arr'); if ($assignment === '') {} else {echo '('.$assignment.')';} ?> <?php if (empty($brpv_file_url)) : ?><?php _e('not created yet', 'brpv'); ?><?php else : ?><?php if ($status_sborki !== -1) : ?><?php _e('updating', 'brpv'); ?><?php else : ?><?php _e('created', 'brpv'); ?><?php endif; ?><?php endif; ?></h2>	
			<div class="inside">
				<p><strong style="color: green;"><?php _e('Instruction', 'brpv'); ?>:</strong> <a href="https://icopydoc.ru/kak-sozdat-woocommerce-yml-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=main-instruction" target="_blank"><?php _e('How to create a YML-feed', 'brpv'); ?></a>.</p>
				<?php if (empty($brpv_file_url)) : ?> 
					<?php if ($status_sborki !== -1) : ?>
						<p><?php _e('We are working on automatic file creation. YML will be developed soon', 'brpv'); ?>.</p>
					<?php else : ?>		
						<p><?php _e('In order to do that, select another menu entry (which differs from "off") in the box called "Automatic file creation". You can also change values in other boxes if necessary, then press "Save"', 'brpv'); ?>.</p>
						<p><?php _e('After 1-7 minutes (depending on the number of products), the feed will be generated and a link will appear instead of this message', 'brpv'); ?>.</p>
					<?php endif; ?>
				<?php else : ?>
					<?php if ($status_sborki !== -1) : ?>
						<p><?php _e('We are working on automatic file creation. YML will be developed soon', 'brpv'); ?>.</p>
					<?php else : ?>
						<p><span class="fgmc_bold"><?php _e('Your feed here', 'brpv'); ?>:</span><br/><a target="_blank" href="<?php echo $brpv_file_url; ?>"><?php echo $brpv_file_url; ?></a>
						<br/><?php _e('File size', 'brpv'); ?>: <?php clearstatcache();
						if ($this->get_feed_id() == '1') {$prefFeed = '';} else {$prefFeed = $this->get_feed_id();}
						$upload_dir = (object)wp_get_upload_dir();
						if (is_multisite()) {
							$filename = $upload_dir->basedir."/".$prefFeed."feed-yml-".get_current_blog_id().".xml";
						} else {
							$filename = $upload_dir->basedir."/".$prefFeed."feed-yml-0.xml";				
						}
						if (is_file($filename)) {echo brpv_formatSize(filesize($filename));} else {echo '0 KB'.$filename;} ?>
						<br/><?php _e('Start of generation', 'brpv'); ?>: <?php echo $brpv_date_sborki; ?>
						<br/><?php _e('Generated', 'brpv'); ?>: <?php echo $brpv_date_sborki_end; ?>
						<br/><?php _e('Products', 'brpv'); ?>: <?php echo $brpv_count_products_in_feed; ?></p>
					<?php endif; ?>		
				<?php endif; ?>
			</div>
		</div><?php
	} // end get_html_info_block();

	public function get_html_tabs($current = 'main_tab') {
		$tabs = array(
			'main_tab' 		=> __('Main settings', 'brpv'),
			'shop_data'		=> __('Shop data', 'brpv'),			
			'tags'			=> __('Attribute settings', 'brpv'), 
			'filtration'	=> __('Filtration', 'brpv')
		);
		
		$html = '<div class="nav-tab-wrapper" style="margin-bottom: 10px;">';
			foreach ($tabs as $tab => $name) {
				if ($tab === $current) {
					$class = ' nav-tab-active';
				} else {
					$class = ''; 
				}
				if (isset($_GET['feed_id'])) {
					$nf = '&feed_id='.sanitize_text_field($_GET['feed_id']);
				} else {
					$nf = '';
				}
				$html .= sprintf('<a class="nav-tab%1$s" href="?page=brpvexport&tab=%2$s%3$s">%4$s</a>',$class, $tab, $nf, $name);
			}
		$html .= '</div>';

		return $html;
	} // end get_html_tabs();

	public function get_html_main_settings() { 	
		$brpv_status_cron = brpv_optionGET('brpv_status_cron', $this->get_feed_id(), 'set_arr'); 
		$brpv_ufup = brpv_optionGET('brpv_ufup', $this->get_feed_id(), 'set_arr');
		$brpv_whot_export = brpv_optionGET('brpv_whot_export', $this->get_feed_id(), 'set_arr'); 
		$brpv_feed_assignment = brpv_optionGET('brpv_feed_assignment', $this->get_feed_id(), 'set_arr');
		$brpv_file_extension = brpv_optionGET('brpv_file_extension', $this->get_feed_id(), 'set_arr');
		$brpv_format_date = brpv_optionGET('brpv_format_date', $this->get_feed_id(), 'set_arr');
		$brpv_yml_rules = brpv_optionGET('brpv_yml_rules', $this->get_feed_id(), 'set_arr');
		$brpv_step_export = brpv_optionGET('brpv_step_export', $this->get_feed_id(), 'set_arr'); 		
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Main parameters', 'brpv'); ?> (<?php _e('Feed', 'brpv'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_run_cron"><?php _e('Automatic file creation', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_run_cron" id="brpv_run_cron">
								<option value="off" <?php selected($brpv_status_cron, 'off' ); ?>><?php _e('Off', 'brpv'); ?></option>
								<?php $brpv_enable_five_min = brpv_optionGET('brpv_enable_five_min'); if ($brpv_enable_five_min === 'on') : ?>
								<option value="five_min" <?php selected($brpv_status_cron, 'five_min');?> ><?php _e('Every five minutes', 'brpv'); ?></option>
								<?php endif; ?>
								<option value="hourly" <?php selected($brpv_status_cron, 'hourly');?> ><?php _e('Hourly', 'brpv'); ?></option>
								<option value="six_hours" <?php selected($brpv_status_cron, 'six_hours'); ?> ><?php _e('Every six hours', 'brpv'); ?></option>	
								<option value="twicedaily" <?php selected($brpv_status_cron, 'twicedaily');?> ><?php _e('Twice a day', 'brpv'); ?></option>
								<option value="daily" <?php selected($brpv_status_cron, 'daily');?> ><?php _e('Daily', 'brpv'); ?></option>
								<option value="week" <?php selected($brpv_status_cron, 'week');?> ><?php _e('Once a week', 'brpv'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('The refresh interval on your feed', 'brpv'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_ufup"><?php _e('Update feed when updating products', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_ufup" id="brpv_ufup" <?php checked($brpv_ufup, 'on' ); ?>/>
						</td>
					</tr>
					<?php do_action('brpv_after_ufup_option', $this->get_feed_id()); /* С версии 3.0.0 */ ?>
					<tr>
						<th scope="row"><label for="brpv_feed_assignment"><?php _e('Feed assignment', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" maxlength="20" name="brpv_feed_assignment" id="brpv_feed_assignment" value="<?php echo $brpv_feed_assignment; ?>" placeholder="<?php _e('For Yandex Market', 'brpv');?>" /><br />
							<span class="description"><small><?php _e('Not used in feed. Inner note for your convenience', 'brpv'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_file_extension"><?php _e('Feed file extension', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_file_extension" id="brpv_file_extension">
								<option value="xml" <?php selected($brpv_file_extension, 'xml'); ?>>XML (<?php _e('recommend', 'brpv'); ?>)</option>
								<option value="yml" <?php selected($brpv_file_extension, 'yml'); ?>>YML</option>
								<?php do_action('brpv_after_file_extension_option', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('Default', 'brpv'); ?>: XML</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_format_date"><?php _e('Format date', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_format_date" id="brpv_format_date">
								<option value="rfc" <?php selected($brpv_format_date, 'rfc'); ?>>RFC 3339 (<?php _e('recommend', 'brpv'); ?>)</option>
								<option value="unixtime" <?php selected($brpv_format_date, 'unixtime'); ?>>Unix time</option>
							</select><br />
							<span class="description"><small><?php _e('Default', 'brpv'); ?>: RFC 3339</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_yml_rules"><?php _e('To follow the rules', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_yml_rules" id="brpv_yml_rules">
							<option value="yandex_market" <?php selected($brpv_yml_rules, 'yandex_market'); ?>><?php _e('Yandex Market', 'brpv'); ?> ADV (<?php _e('Simplified type', 'brpv'); ?>)</option>
							<option value="dbs" <?php selected($brpv_yml_rules, 'dbs'); ?>><?php _e('Yandex Market', 'brpv'); ?> DBS (<?php _e('Simplified type', 'brpv'); ?>)</option>
							<option value="single_catalog" <?php selected($brpv_yml_rules, 'single_catalog'); ?>>FBY, FBY+, FBS (<?php _e('in a single catalog', 'brpv'); ?>) (beta)</option>
							<option value="sbermegamarket" <?php selected($brpv_yml_rules, 'sbermegamarket'); ?>><?php _e('SberMegaMarket', 'brpv'); ?> (beta)</option>
							<option value="beru" <?php selected($brpv_yml_rules, 'beru'); ?>><?php _e('Former Beru', 'brpv'); ?></option>
							<option value="yandex_webmaster" <?php selected($brpv_yml_rules, 'yandex_webmaster'); ?>><?php _e('Yandex Webmaster', 'brpv'); ?> (turbo)</option>
							<option value="all_elements" <?php selected($brpv_yml_rules, 'all_elements'); ?>><?php _e('No rules', 'brpv'); ?> (<?php _e('Not recommended', 'brpv'); ?>)</option>
							<option value="ozon" <?php selected($brpv_yml_rules, 'ozon'); ?>>OZON</option>
							<?php do_action('brpv_append_select_brpv_yml_rules', $brpv_yml_rules, $this->get_feed_id()); ?>
							</select><br />
							<?php do_action('brpv_after_select_brpv_yml_rules', $brpv_yml_rules, $this->get_feed_id()); ?>
							<span class="description"><small><?php _e('Exclude products that do not meet the requirements', 'brpv'); ?> <i>(<?php _e('missing required elements/data', 'brpv'); ?>)</i>. <?php _e('The plugin will try to automatically remove products from the YML-feed for which the required fields for the feed are not filled', 'brpv'); ?>. <?php _e('Also, this item affects the structure of the file', 'brpv'); ?>.</small></span>
						</td>
					</tr>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_step_export"><?php _e('Step of export', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_step_export" id="brpv_step_export">
							<option value="80" <?php selected($brpv_step_export, '80'); ?>>80</option>
							<option value="200" <?php selected($brpv_step_export, '200'); ?>>200</option>
							<option value="300" <?php selected($brpv_step_export, '300'); ?>>300</option>
							<option value="450" <?php selected($brpv_step_export, '450'); ?>>450</option>
							<option value="500" <?php selected($brpv_step_export, '500'); ?>>500</option>
							<option value="800" <?php selected($brpv_step_export, '800'); ?>>800</option>
							<option value="1000" <?php selected($brpv_step_export, '1000'); ?>>1000</option>
							<?php do_action('brpv_step_export_option', $this->get_feed_id(), $brpv_step_export); ?>
							</select><br />
							<span class="description"><small><?php _e('The value affects the speed of file creation', 'brpv'); ?>. <?php _e('If you have any problems with the generation of the file - try to reduce the value in this field', 'brpv'); ?>. <?php _e('More than 500 can only be installed on powerful servers', 'brpv'); ?>.</small></span>
						</td>
					</tr>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_main_settings();

	public function get_html_shop_data() { 
		$brpv_shop_name = stripslashes(htmlspecialchars(brpv_optionGET('brpv_shop_name', $this->get_feed_id(), 'set_arr')));
		$brpv_company_name = stripslashes(htmlspecialchars(brpv_optionGET('brpv_company_name', $this->get_feed_id(), 'set_arr')));
		$brpv_warehouse = stripslashes(htmlspecialchars(brpv_optionGET('brpv_warehouse', $this->get_feed_id(), 'set_arr')));
		$brpv_currencies = brpv_optionGET('brpv_currencies', $this->get_feed_id(), 'set_arr');
		$brpv_main_product = brpv_optionGET('brpv_main_product', $this->get_feed_id(), 'set_arr');
		$brpv_adult = brpv_optionGET('brpv_adult', $this->get_feed_id(), 'set_arr'); 	
		$brpv_wooc_currencies = brpv_optionGET('brpv_wooc_currencies', $this->get_feed_id(), 'set_arr');
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Shop data', 'brpv'); ?> (<?php _e('Feed', 'brpv'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_shop_name"><?php _e('Shop name', 'brpv'); ?></label></th>
						<td class="overalldesc">
						<input maxlength="20" type="text" name="brpv_shop_name" id="brpv_shop_name" value="<?php echo $brpv_shop_name; ?>" /><br />
						<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>name</strong>. <?php _e('The short name of the store should not exceed 20 characters', 'brpv'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_company_name"><?php _e('Company name', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_company_name" id="brpv_company_name" value="<?php echo $brpv_company_name; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>company</strong>. <?php _e('Full name of the company that owns the store', 'brpv'); ?>.</small></span>
						</td>
					</tr>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_warehouse"><?php _e('Warehouse', 'brpv'); ?> Name/ID</label></th>
						<td class="overalldesc">
						<input type="text" name="brpv_warehouse" id="brpv_warehouse" value="<?php echo $brpv_warehouse; ?>" /><br />
						<span class="description"><small><?php _e('Warehouse name', 'brpv'); ?> (OZON) <?php _e('or ID', 'brpv'); ?> (<?php _e('SberMegaMarket', 'brpv'); ?>)</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_currencies"><?php _e('Element "currencies"', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_currencies" id="brpv_currencies">
								<option value="enabled" <?php selected($brpv_currencies, 'enabled'); ?>><?php _e('Enabled', 'brpv'); ?></option>	
								<option value="disabled" <?php selected($brpv_currencies, 'disabled'); ?>><?php _e('Disabled', 'brpv'); ?></option>
							</select>
						</td>
					</tr>		 
					<tr>
						<th scope="row"><label for="brpv_main_product"><?php _e('What kind of products do you sell', 'brpv'); ?>?</label></th>
						<td class="overalldesc">
								<select name="brpv_main_product" id="brpv_main_product">
								<option value="electronics" <?php selected($brpv_main_product, 'electronics'); ?>><?php _e('Electronics', 'brpv'); ?></option>
								<option value="computer" <?php selected($brpv_main_product, 'computer'); ?>><?php _e('Computer techologies', 'brpv'); ?></option>
								<option value="clothes_and_shoes" <?php selected($brpv_main_product, 'clothes_and_shoes'); ?>><?php _e('Clothes and shoes', 'brpv'); ?></option>
								<option value="auto_parts" <?php selected($brpv_main_product, 'auto_parts'); ?>><?php _e('Auto parts', 'brpv'); ?></option>
								<option value="products_for_children" <?php selected($brpv_main_product, 'products_for_children'); ?>><?php _e('Products for children', 'brpv'); ?></option>
								<option value="sporting_goods" <?php selected($brpv_main_product, 'sporting_goods'); ?>><?php _e('Sporting goods', 'brpv'); ?></option>
								<option value="goods_for_pets" <?php selected($brpv_main_product, 'goods_for_pets'); ?>><?php _e('Goods for pets', 'brpv'); ?></option>
								<option value="sexshop" <?php selected($brpv_main_product, 'sexshop'); ?>><?php _e('Sex shop (Adult products)', 'brpv'); ?></option>
								<option value="books" <?php selected($brpv_main_product, 'books'); ?>><?php _e('Books', 'brpv'); ?></option>
								<option value="health" <?php selected($brpv_main_product, 'health'); ?>><?php _e('Health products', 'brpv'); ?></option>	
								<option value="food" <?php selected($brpv_main_product, 'food'); ?>><?php _e('Food', 'brpv'); ?></option>
								<option value="construction_materials" <?php selected($brpv_main_product, 'construction_materials'); ?>><?php _e('Construction Materials', 'brpv'); ?></option>
								<option value="other" <?php selected($brpv_main_product, 'other'); ?>><?php _e('Other', 'brpv'); ?></option>	
							</select><br />
							<span class="description"><small><?php _e('Specify the main category', 'brpv'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_adult"><?php _e('Adult Market', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_adult" id="brpv_adult">
							<option value="no" <?php selected($brpv_adult, 'no'); ?>><?php _e('No', 'brpv'); ?></option>
							<option value="yes" <?php selected($brpv_adult, 'yes'); ?>><?php _e('Yes', 'brpv'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Optional element', 'brpv'); ?> <strong>adult</strong></small></span>
						</td>
					</tr>
					<?php if (class_exists('WOOCS')) : 		 
						global $WOOCS; $currencies_arr = $WOOCS->get_currencies(); 
						if (is_array($currencies_arr)) : $array_keys = array_keys($currencies_arr); ?>
						<tr>
							<th scope="row"><label for="brpv_wooc_currencies"><?php _e('Feed currency', 'brpv'); ?></label></th>
							<td class="overalldesc">
								<select name="brpv_wooc_currencies" id="brpv_wooc_currencies">
								<?php for ($i = 0; $i < count($array_keys); $i++) : ?>
									<option value="<?php echo $currencies_arr[$array_keys[$i]]['name']; ?>" <?php selected($brpv_wooc_currencies, $currencies_arr[$array_keys[$i]]['name']); ?>><?php echo $currencies_arr[$array_keys[$i]]['name']; ?></option>					
								<?php endfor; ?>
								</select><br />
								<span class="description"><small><?php _e('You have plugin installed', 'brpv'); ?> <strong class="brpv_bold">WooCommerce Currency Switcher by PluginUs.NET. Woo Multi Currency and Woo Multi Pay</strong><br />
								<?php _e('Indicate in what currency the prices should be', 'brpv'); ?>.<br /><strong class="brpv_bold"><?php _e('Please note', 'brpv'); ?>:</strong> <?php _e('Yandex Market only supports the following currencies', 'brpv'); ?>: RUR, RUB, UAH, BYN, KZT, USD, EUR. <?php _e('Choosing a different currency can lead to errors', 'brpv'); ?>
								</small></span>
							</td>
						</tr>
						<?php endif; ?>
					<?php endif; ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_shop_data();

	public function get_html_tags_settings() {
		$brpv_no_group_id_arr = unserialize(brpv_optionGET('brpv_no_group_id_arr', $this->get_feed_id())); 
		$add_in_name_arr = unserialize(brpv_optionGET('brpv_add_in_name_arr', $this->get_feed_id()));
		$brpv_separator_type = brpv_optionGET('brpv_separator_type', $this->get_feed_id(), 'set_arr'); 

		$brpv_pickup_options = brpv_optionGET('brpv_pickup_options', $this->get_feed_id(), 'set_arr');
		$brpv_pickup_cost = brpv_optionGET('brpv_pickup_cost', $this->get_feed_id(), 'set_arr'); 
		$brpv_pickup_days = brpv_optionGET('brpv_pickup_days', $this->get_feed_id(), 'set_arr'); 
		$brpv_pickup_order_before = brpv_optionGET('brpv_pickup_order_before', $this->get_feed_id(), 'set_arr');

		$brpv_delivery_options = brpv_optionGET('brpv_delivery_options', $this->get_feed_id(), 'set_arr');
		$brpv_delivery_cost = brpv_optionGET('brpv_delivery_cost', $this->get_feed_id(), 'set_arr'); 
		$brpv_delivery_days = brpv_optionGET('brpv_delivery_days', $this->get_feed_id(), 'set_arr'); 
		$brpv_order_before = brpv_optionGET('brpv_order_before', $this->get_feed_id(), 'set_arr');
		$brpv_delivery_options2 = brpv_optionGET('brpv_delivery_options2', $this->get_feed_id(), 'set_arr');
		$brpv_delivery_cost2 = brpv_optionGET('brpv_delivery_cost2', $this->get_feed_id(), 'set_arr'); 
		$brpv_delivery_days2 = brpv_optionGET('brpv_delivery_days2', $this->get_feed_id(), 'set_arr');  
		$brpv_order_before2 = brpv_optionGET('brpv_order_before2', $this->get_feed_id(), 'set_arr');
		$brpv_ebay_stock = brpv_optionGET('brpv_ebay_stock', $this->get_feed_id(), 'set_arr'); 
		$params_arr = unserialize(brpv_optionGET('brpv_params_arr', $this->get_feed_id()));
		?>
		<?php do_action('brpv_optional_element', $this->get_feed_id()); ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Tags settings', 'brpv'); ?> (<?php _e('Feed', 'brpv'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_no_group_id_arr"><?php _e('Categories of variable products for which group_id is not allowed', 'brpv'); ?></label></th>
						<td class="overalldesc">
						<select id="brpv_no_group_id_arr" style="width: 100%;" name="brpv_no_group_id_arr[]" size="8" multiple>
							<?php foreach (get_terms('product_cat', array('hide_empty'=>0, 'parent'=>0)) as $term) {
									echo brpv_cat_tree($term->taxonomy, $term->term_id, $brpv_no_group_id_arr); } ?>
						</select><br />
						<span class="description"><small><?php _e('According to Yandex Market rules in this field you need to mark ALL categories of products not related to "Clothes, Shoes and Accessories", "Furniture", "Cosmetics, perfumes and care", "Baby products", "Accessories for portable electronics". Ie categories for which it is forbidden to use the attribute group_id', 'brpv'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_add_in_name_arr"><?php _e('Add attributes to the variable products name', 'brpv'); ?></label><br />(<?php _e('You can only add attributes that are used for variations and that cannot be grouped using', 'brpv'); ?> group_id)</th>
						<td class="overalldesc">
						<select id="brpv_add_in_name_arr" style="width: 100%;" name="brpv_add_in_name_arr[]" size="8" multiple>
							<?php foreach (brpv_get_attributes() as $attribute) : ?>
								<option value="<?php echo $attribute['id']; ?>"<?php if (!empty($add_in_name_arr)) { foreach ($add_in_name_arr as $value) {selected($value, $attribute['id']);}} ?>><?php echo $attribute['name']; ?></option>
							<?php endforeach; ?>
						</select><br />
						<span class="description"><small><?php _e('It works only for variable products that are not in the category "Clothes, Shoes and Accessories", "Furniture", "Cosmetics, perfumes and care", "Baby products", "Accessories for portable electronics"', 'brpv'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_separator_type"><?php _e('Separator options', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_separator_type" id="brpv_separator_type">
								<option value="type1" <?php selected($brpv_separator_type, 'type1'); ?>><?php _e('Type', 'brpv'); ?>_1 (В1:З1, В2:З2, ... Вn:Зn)</option>
								<option value="type2" <?php selected($brpv_separator_type, 'type2')?> ><?php _e('Type', 'brpv'); ?>_2 (В1-З1, В2-З2, ... Вn:Зn)</option>
								<option value="type3" <?php selected($brpv_separator_type, 'type3'); ?> ><?php _e('Type', 'brpv'); ?>_3 В1:З1, В2:З2, ... Вn:Зn</option>
								<option value="type4" <?php selected($brpv_separator_type, 'type4'); ?> ><?php _e('Type', 'brpv'); ?>_4 З1 З2 ... Зn</option>
								<?php do_action('brpv_after_option_separator_type', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('Separator options', 'brpv'); ?></small></span>
						</td>
					</tr>

					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_pickup_options"><?php _e('Add', 'brpv'); ?> pickup-options<br/><small>(<?php _e('pickup of products', 'brpv'); ?>)</small></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_pickup_options" id="brpv_pickup_options" <?php checked($brpv_pickup_options, 'on' ); ?>/><br />
							<span class="description"><small><?php _e('Optional element', 'brpv'); ?> <strong>pickup-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/pickup-options.html#structure"><?php _e('Read more on Yandex', 'brpv'); ?></a></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_pickup_cost"><?php _e('Pickup cost', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input min="0" type="number" name="brpv_pickup_cost" id="brpv_pickup_cost" value="<?php echo $brpv_pickup_cost; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>cost</strong> <?php _e('of attribute', 'brpv'); ?> <strong>pickup-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_pickup_days"><?php _e('Pickup days', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_pickup_days" id="brpv_pickup_days" value="<?php echo $brpv_pickup_days; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>days</strong> <?php _e('of attribute', 'brpv'); ?> <strong>pickup-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_pickup_order_before"><?php _e('The time', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_pickup_order_before" id="brpv_pickup_order_before" value="<?php echo $brpv_pickup_order_before; ?>" /><br />
							<span class="description"><small><?php _e('Optional element', 'brpv'); ?> <strong>order-before</strong> <?php _e('of attribute', 'brpv'); ?> <strong>pickup-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'brpv'); ?></small></span>
						</td>
					</tr>

					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_delivery_options"><?php _e('Use delivery-options', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_delivery_options" id="brpv_delivery_options" <?php checked($brpv_delivery_options, 'on' ); ?>/><br />
							<span class="description"><small><?php _e('Optional element', 'brpv'); ?> <strong>delivery-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure"><?php _e('Read more on Yandex', 'brpv'); ?></a></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_delivery_cost"><?php _e('Delivery cost', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input min="0" type="number" name="brpv_delivery_cost" id="brpv_delivery_cost" value="<?php echo $brpv_delivery_cost; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>cost</strong> <?php _e('of attribute', 'brpv'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_delivery_days"><?php _e('Delivery days', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_delivery_days" id="brpv_delivery_days" value="<?php echo $brpv_delivery_days; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>days</strong> <?php _e('of attribute', 'brpv'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_order_before"><?php _e('The time', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_order_before" id="brpv_order_before" value="<?php echo $brpv_order_before; ?>" /><br />
							<span class="description"><small><?php _e('Optional element', 'brpv'); ?> <strong>order-before</strong> <?php _e('of attribute', 'brpv'); ?> <strong>delivery-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'brpv'); ?></small></span>
						</td>
					</tr>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_delivery_options2"><?php _e('Add a second delivery methods', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_delivery_options2" id="brpv_delivery_options2" <?php checked($brpv_delivery_options2, 'on' ); ?>/><br />
							<span class="description"><small><?php _e('Add a second delivery methods to', 'brpv'); ?> <strong>delivery-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure"><?php _e('Read more on Yandex', 'brpv'); ?></a></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_delivery_cost2"><?php _e('Delivery cost', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input min="0" type="number" name="brpv_delivery_cost2" id="brpv_delivery_cost2" value="<?php echo $brpv_delivery_cost2; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>cost</strong> <?php _e('of attribute', 'brpv'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_delivery_days2"><?php _e('Delivery days', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_delivery_days2" id="brpv_delivery_days2" value="<?php echo $brpv_delivery_days2; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'brpv'); ?> <strong>days</strong> <?php _e('of attribute', 'brpv'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_order_before2"><?php _e('The time', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_order_before2" id="brpv_order_before2" value="<?php echo $brpv_order_before2; ?>" /><br />
							<span class="description"><small><?php _e('Optional element', 'brpv'); ?> <strong>order-before</strong> <?php _e('of attribute', 'brpv'); ?> <strong>delivery-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'brpv'); ?></small></span>
						</td>
					</tr>	
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_ebay_stock"><?php _e('Add information about stock to feed for EBay', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" id="brpv_ebay_stock" name="brpv_ebay_stock" <?php checked($brpv_ebay_stock, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_params_arr"><?php _e('Include these attributes in the values Param', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select id="brpv_params_arr" style="width: 100%;" name="brpv_params_arr[]" size="8" multiple>
								<?php foreach (brpv_get_attributes() as $attribute) : ?>
									<option value="<?php echo $attribute['id']; ?>"<?php if (!empty($params_arr)) {foreach ($params_arr as $value) {selected($value, $attribute['id']);}} ?>><?php echo $attribute['name']; ?></option>
								<?php endforeach; ?>
							</select><br />
							<span class="description"><small>
								<?php _e('Optional element', 'brpv'); ?> <strong>param</strong></span><br />
								<span style="color: blue;"><?php _e('Hint', 'brpv'); ?>:</span> <?php _e('To select multiple values, hold down the (ctrl) button on Windows or (cmd) on a Mac. To deselect, press and hold (ctrl) or (cmd), click on the marked items', 'brpv'); ?>
							</small></span>
						</td>
					</tr>
					<?php do_action('brpv_after_manufacturer_warranty', $this->get_feed_id()); ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_tags_settings();

	public function get_html_filtration() { 
		$brpv_whot_export = brpv_optionGET('brpv_whot_export', $this->get_feed_id(), 'set_arr'); 
		$brpv_desc = brpv_optionGET('brpv_desc', $this->get_feed_id(), 'set_arr');
//		$brpv_del_all_attributes = brpv_optionGET('brpv_del_all_attributes', $this->get_feed_id(), 'set_arr');
		$brpv_enable_tags_custom = brpv_optionGET('brpv_enable_tags_custom', $this->get_feed_id(), 'set_arr');
		$brpv_enable_tags_behavior = brpv_optionGET('brpv_enable_tags_behavior', $this->get_feed_id(), 'set_arr');
		$brpv_the_content = brpv_optionGET('brpv_the_content', $this->get_feed_id(), 'set_arr');
		$brpv_replace_domain = brpv_optionGET('brpv_replace_domain', $this->get_feed_id(), 'set_arr');
		$brpv_var_desc_priority = brpv_optionGET('brpv_var_desc_priority', $this->get_feed_id(), 'set_arr');
		$brpv_clear_get = brpv_optionGET('brpv_clear_get', $this->get_feed_id(), 'set_arr');
		$brpv_behavior_onbackorder = brpv_optionGET('brpv_behavior_onbackorder', $this->get_feed_id(), 'set_arr'); 
		$brpv_behavior_stip_symbol = brpv_optionGET('brpv_behavior_stip_symbol', $this->get_feed_id(), 'set_arr');
		$brpv_skip_missing_products = brpv_optionGET('brpv_skip_missing_products', $this->get_feed_id(), 'set_arr');
		$brpv_skip_backorders_products = brpv_optionGET('brpv_skip_backorders_products', $this->get_feed_id(), 'set_arr'); 
		$brpv_no_default_png_products = brpv_optionGET('brpv_no_default_png_products', $this->get_feed_id(), 'set_arr');
		$brpv_skip_products_without_pic = brpv_optionGET('brpv_skip_products_without_pic', $this->get_feed_id(), 'set_arr'); 
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Filtration', 'brpv'); ?> (<?php _e('Feed', 'brpv'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_whot_export"><?php _e('Whot export', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_whot_export" id="brpv_whot_export">
								<option value="all" <?php selected($brpv_whot_export, 'all'); ?>><?php _e('Simple & Variable products', 'brpv'); ?></option>
								<option value="simple" <?php selected($brpv_whot_export, 'simple'); ?>><?php _e('Only simple products', 'brpv'); ?></option>
								<?php do_action('brpv_after_whot_export_option', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('Whot export', 'brpv'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_desc"><?php _e('Description of the product', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_desc" id="brpv_desc">
							<option value="excerpt" <?php selected($brpv_desc, 'excerpt'); ?>><?php _e('Only Excerpt description', 'brpv'); ?></option>
							<option value="full" <?php selected($brpv_desc, 'full'); ?>><?php _e('Only Full description', 'brpv'); ?></option>
							<option value="excerptfull" <?php selected($brpv_desc, 'excerptfull'); ?>><?php _e('Excerpt or Full description', 'brpv'); ?></option>
							<option value="fullexcerpt" <?php selected($brpv_desc, 'fullexcerpt'); ?>><?php _e('Full or Excerpt description', 'brpv'); ?></option>
							<option value="excerptplusfull" <?php selected($brpv_desc, 'excerptplusfull'); ?>><?php _e('Excerpt plus Full description', 'brpv'); ?></option>
							<option value="fullplusexcerpt" <?php selected($brpv_desc, 'fullplusexcerpt'); ?>><?php _e('Full plus Excerpt description', 'brpv'); ?></option>
							<?php do_action('brpv_append_select_brpv_desc', $brpv_desc, $this->get_feed_id()); /* с версии 3.2.1 */ ?>
							</select><br />
							<?php do_action('brpv_after_select_brpv_desc', $brpv_desc, $this->get_feed_id()); /* с версии 3.2.1 */ ?>
							<span class="description"><small><?php _e('The source of the description', 'brpv'); ?></small></span>
						</td>
					</tr><?php /*
					<tr>
						<th scope="row"><label for="brpv_del_all_attributes"><?php _e('Remove all attributes in tags', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_del_all_attributes" id="brpv_del_all_attributes">
							<option value="disabled" <?php selected($brpv_del_all_attributes, 'disabled'); ?>><?php _e('Disabled', 'brpv'); ?></option>
							<option value="enabled" <?php selected($brpv_del_all_attributes, 'enabled'); ?>><?php _e('Enabled', 'brpv'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Remove all attributes in tags from product description', 'brpv'); ?></small></span>
						</td>
					</tr> */ ?>
					<tr>
						<th scope="row"><label for="brpv_enable_tags_custom"><?php _e('List of allowed tags', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_enable_tags_behavior" id="brpv_enable_tags_behavior">
								<option value="default" <?php selected($brpv_enable_tags_behavior, 'default'); ?>><?php _e('Default', 'brpv'); ?></option>
								<option value="custom" <?php selected($brpv_enable_tags_behavior, 'custom'); ?>><?php _e('From the field below', 'brpv'); ?></option>
							</select><br />
							<input style="min-width: 100%;" type="text" name="brpv_enable_tags_custom" id="brpv_enable_tags_custom" value="<?php echo $brpv_enable_tags_custom; ?>" placeholder="p,br,h3" /><br />
							<span class="description"><small><?php _e('For example', 'brpv'); ?>: <code>p,br,h3</code></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_the_content"><?php _e('Use the filter', 'brpv'); ?> the_content</label></th>
						<td class="overalldesc">
							<select name="brpv_the_content" id="brpv_the_content">
							<option value="disabled" <?php selected($brpv_the_content, 'disabled'); ?>><?php _e('Disabled', 'brpv'); ?></option>
							<option value="enabled" <?php selected($brpv_the_content, 'enabled'); ?>><?php _e('Enabled', 'brpv'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Default', 'brpv'); ?>: <?php _e('Enabled', 'brpv'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_replace_domain"><?php _e('Change the domain to', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="brpv_replace_domain" id="brpv_replace_domain" value="<?php echo $brpv_replace_domain; ?>" placeholder="https://site.ru" /><br />
							<span class="description"><small><?php _e('The option allows you to change the domain of your site in the feed to any other', 'brpv'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_var_desc_priority"><?php _e('The varition description takes precedence over others', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_var_desc_priority" id="brpv_var_desc_priority" <?php checked($brpv_var_desc_priority, 'on'); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_clear_get"><?php _e('Clear URL from GET-paramrs', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_clear_get" id="brpv_clear_get">
							<option value="no" <?php selected($brpv_clear_get, 'no'); ?>><?php _e('No', 'brpv'); ?></option>
							<option value="yes" <?php selected($brpv_clear_get, 'yes'); ?>><?php _e('Yes', 'brpv'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('This option may be useful when setting up Turbo pages', 'brpv'); ?><br />
							<a target="_blank" href="https://icopydoc.ru/vklyuchaem-turbo-stranitsy-dlya-magazina-woocommerce-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=yandex-turbo-instruction"><?php _e('Tips for configuring Turbo pages', 'brpv'); ?></a></small></span>
						</td>
					</tr>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_behavior_onbackorder"><?php _e('For pre-order products, establish availability equal to', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_behavior_onbackorder" id="brpv_behavior_onbackorder">
								<option value="false" <?php selected($brpv_behavior_onbackorder, 'false'); ?>>False</option>
								<option value="true" <?php selected($brpv_behavior_onbackorder, 'true')?> >True</option>
							</select><br />
							<span class="description"><small><?php _e('For pre-order products, establish availability equal to', 'brpv'); ?> false/true</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_behavior_stip_symbol"><?php _e('In attributes', 'brpv'); ?> vendorCode <?php _e('and', 'brpv'); ?> shop-sku <?php _e('ampersand', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<select name="brpv_behavior_stip_symbol" id="brpv_behavior_stip_symbol">
								<option value="default" <?php selected($brpv_behavior_stip_symbol, 'default'); ?>><?php _e('Default', 'brpv'); ?></option>
								<option value="del" <?php selected($brpv_behavior_stip_symbol, 'del'); ?>><?php _e('Delete', 'brpv'); ?></option>
								<option value="slash" <?php selected($brpv_behavior_stip_symbol, 'slash'); ?>><?php _e('Replace with', 'brpv'); ?> /</option>
								<option value="amp" <?php selected($brpv_behavior_stip_symbol, 'amp'); ?>><?php _e('Replace with', 'brpv'); ?> amp;</option>
							</select><br />
							<span class="description"><small><?php _e('Default', 'brpv'); ?> "<?php _e('Delete', 'brpv'); ?>"</small></span>
						</td>
					</tr>
					<tr class="brpv_tr">
						<th scope="row"><label for="brpv_skip_missing_products"><?php _e('Skip missing products', 'brpv'); ?> (<?php _e('except for products for which a pre-order is permitted', 'brpv'); ?>.)</label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_skip_missing_products" id="brpv_skip_missing_products" <?php checked($brpv_skip_missing_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_skip_backorders_products"><?php _e('Skip backorders products', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_skip_backorders_products" id="brpv_skip_backorders_products" <?php checked($brpv_skip_backorders_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_no_default_png_products"><?php _e('Remove default.png from YML', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_no_default_png_products" id="brpv_no_default_png_products" <?php checked($brpv_no_default_png_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="brpv_skip_products_without_pic"><?php _e('Skip products without pictures', 'brpv'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="brpv_skip_products_without_pic" id="brpv_skip_products_without_pic" <?php checked($brpv_skip_products_without_pic, 'on' ); ?>/>
						</td>
					</tr>
					<?php do_action('brpv_after_step_export', $this->get_feed_id()); ?>

					<?php do_action('brpv_append_main_param_table', $this->get_feed_id()); ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_filtration();

	public function get_html_icp_banners() { ?>
		<div id="icp_slides" class="clear">
			<div class="icp_wrap">
				<input type="radio" name="icp_slides" id="icp_point1">
				<input type="radio" name="icp_slides" id="icp_point2">
				<input type="radio" name="icp_slides" id="icp_point3">
				<input type="radio" name="icp_slides" id="icp_point4">
				<input type="radio" name="icp_slides" id="icp_point5" checked>
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
		</div><?php 
	} // end get_html_icp_banners()

	public function get_html_my_plugins_list() { ?>
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
		</div><?php
	} // end get_html_my_plugins_list()

	public function admin_head_css_func() {
		/* печатаем css в шапке админки */
		print '<style>/* Best Rating & Pageviews */
			.metabox-holder .postbox-container .empty-container {height: auto !important;}
			.icp_img1 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl1.jpg);}
			.icp_img2 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl2.jpg);}
			.icp_img3 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl3.jpg);}
			.icp_img4 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl4.jpg);}
			.icp_img5 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl5.jpg);}
			.icp_img6 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl6.jpg);}
			.icp_img7 {background-image: url('. BRPV_PLUGIN_DIR_URL .'img/sl7.jpg);}
		</style>';
	}

	private function init_hooks() {
		// наш класс, вероятно, вызывается во время срабатывания хука admin_menu.
		// admin_init - следующий в очереди срабатывания, хуки раньше admin_menu нет смысла вешать
		// add_action('admin_init', array($this, 'listen_submits'), 10);
		add_action('admin_print_footer_scripts', array($this, 'admin_head_css_func'));
	}

	private function get_feed_id() {
		return $this->feed_id;
	}

	private function save_plugin_set($opt_name, $feed_id, $save_if_empty = false) {
		if (isset($_POST[$opt_name])) {
			brpv_optionUPD($opt_name, sanitize_text_field($_POST[$opt_name]), $feed_id, 'yes', 'set_arr');
		} else {
			if ($save_if_empty === true) {
				brpv_optionUPD($opt_name, '0', $feed_id, 'yes', 'set_arr');
			}
		}
		return;
	}

	private function listen_submit() {
	// массовое удаление фидов по чекбоксу checkbox_xml_file
		if (isset($_GET['brpv_form_id']) && ($_GET['brpv_form_id'] === 'brpv_wp_list_table')) {
			if (is_array($_GET['checkbox_xml_file']) && !empty($_GET['checkbox_xml_file'])) {
				if ($_GET['action'] === 'delete' || $_GET['action2'] === 'delete') {
					$checkbox_xml_file_arr = $_GET['checkbox_xml_file'];
					$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
					for ($i = 0; $i < count($checkbox_xml_file_arr); $i++) {
						$feed_id = $checkbox_xml_file_arr[$i];
						unset($brpv_settings_arr[$feed_id]);
						wp_clear_scheduled_hook('brpv_cron_period', array($feed_id)); // отключаем крон
						wp_clear_scheduled_hook('brpv_cron_sborki', array($feed_id)); // отключаем крон
						$upload_dir = (object)wp_get_upload_dir();
						$name_dir = $upload_dir->basedir."/best-rating-pageviews";
		//				$filename = $name_dir.'/ids_in_xml.tmp'; if (file_exists($filename)) {unlink($filename);}
						brpv_remove_directory($name_dir.'/feed'.$feed_id);
						brpv_optionDEL('brpv_status_sborki', $i);

						$brpv_registered_feeds_arr = brpv_optionGET('brpv_registered_feeds_arr');
						for ($n = 1; $n < count($brpv_registered_feeds_arr); $n++) { // первый элемент не проверяем, тк. там инфо по последнему id
							if ($brpv_registered_feeds_arr[$n]['id'] === $feed_id) {
								unset($brpv_registered_feeds_arr[$n]);
								$brpv_registered_feeds_arr = array_values($brpv_registered_feeds_arr);
								brpv_optionUPD('brpv_registered_feeds_arr', $brpv_registered_feeds_arr);
								break;
							}
						}
					}
					brpv_optionUPD('brpv_settings_arr', $brpv_settings_arr);
					$feed_id = brpv_get_first_feed_id();
				}
			}
		}

		if (isset($_GET['feed_id'])) {
			if (isset($_GET['action'])) {
				$action = sanitize_text_field($_GET['action']);
				switch ($action) {
					case 'edit':
						$feed_id = sanitize_text_field($_GET['feed_id']);
						break;
					case 'delete':
						$feed_id = sanitize_text_field($_GET['feed_id']);
						$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
						unset($brpv_settings_arr[$feed_id]);
						wp_clear_scheduled_hook('brpv_cron_period', array($feed_id)); // отключаем крон
						wp_clear_scheduled_hook('brpv_cron_sborki', array($feed_id)); // отключаем крон
						$upload_dir = (object)wp_get_upload_dir();
						$name_dir = $upload_dir->basedir."/best-rating-pageviews";
		//				$filename = $name_dir.'/ids_in_xml.tmp'; if (file_exists($filename)) {unlink($filename);}
						brpv_remove_directory($name_dir.'/feed'.$feed_id);		
						brpv_optionUPD('brpv_settings_arr', $brpv_settings_arr);
						brpv_optionDEL('brpv_status_sborki', $feed_id);					
						$brpv_registered_feeds_arr = brpv_optionGET('brpv_registered_feeds_arr');
						for ($n = 1; $n < count($brpv_registered_feeds_arr); $n++) { // первый элемент не проверяем, тк. там инфо по последнему id
							if ($brpv_registered_feeds_arr[$n]['id'] === $feed_id) {
								unset($brpv_registered_feeds_arr[$n]);
								$brpv_registered_feeds_arr = array_values($brpv_registered_feeds_arr); 
								brpv_optionUPD('brpv_registered_feeds_arr', $brpv_registered_feeds_arr);
								break;
							}
						}
		
						$feed_id = brpv_get_first_feed_id();
						break;
					default:
						$feed_id = brpv_get_first_feed_id();
				}
			} else {$feed_id = sanitize_text_field($_GET['feed_id']);}
		} else {$feed_id = brpv_get_first_feed_id();}

		if (isset($_REQUEST['brpv_submit_add_new_feed'])) { // если создаём новый фид
			if (!empty($_POST) && check_admin_referer('brpv_nonce_action_add_new_feed', 'brpv_nonce_field_add_new_feed')) {
				$brpv_settings_arr = brpv_optionGET('brpv_settings_arr');
				
				if (is_multisite()) {
					$brpv_registered_feeds_arr = get_blog_option(get_current_blog_id(), 'brpv_registered_feeds_arr');
					$feed_id = $brpv_registered_feeds_arr[0]['last_id'];
					$feed_id++;
					$brpv_registered_feeds_arr[0]['last_id'] = (string)$feed_id;
					$brpv_registered_feeds_arr[] = array('id' => (string)$feed_id);
					update_blog_option(get_current_blog_id(), 'brpv_registered_feeds_arr', $brpv_registered_feeds_arr);
				} else {
					$brpv_registered_feeds_arr = get_option('brpv_registered_feeds_arr');
					$feed_id = $brpv_registered_feeds_arr[0]['last_id'];
					$feed_id++;
					$brpv_registered_feeds_arr[0]['last_id'] = (string)$feed_id;
					$brpv_registered_feeds_arr[] = array('id' => (string)$feed_id);
					update_option('brpv_registered_feeds_arr', $brpv_registered_feeds_arr);
				}

				$upload_dir = (object)wp_get_upload_dir();
				$name_dir = $upload_dir->basedir.'/best-rating-pageviews/feed'.$feed_id;
				if (!is_dir($name_dir)) {
					if (!mkdir($name_dir)) {
						error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: export.php; Строка: '.__LINE__, 0);
					}
				}

				$def_plugin_date_arr = new BRPV_Data_Arr();
				$brpv_settings_arr[$feed_id] = $def_plugin_date_arr->get_opts_name_and_def_date('all');

				brpv_optionUPD('brpv_settings_arr', $brpv_settings_arr);
		
				brpv_optionADD('brpv_status_sborki', '-1', $feed_id);
				brpv_optionADD('brpv_last_element', '-1', $feed_id);
				print '<div class="updated notice notice-success is-dismissible"><p>'. __('Feed added', 'brpv').'. ID = '.$feed_id.'.</p></div>';
			}
		}

		$status_sborki = (int)brpv_optionGET('brpv_status_sborki', $feed_id);

		if (isset($_REQUEST['brpv_submit_action'])) {
			if (!empty($_POST) && check_admin_referer('brpv_nonce_action', 'brpv_nonce_field')) {
				do_action('brpv_prepend_submit_action', $feed_id);
			
				$feed_id = sanitize_text_field($_POST['brpv_num_feed_for_save']);
			
				$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT (Unix формат)
				brpv_optionUPD('brpv_date_save_set', $unixtime, $feed_id, 'yes', 'set_arr');
				
				if (isset($_POST['brpv_run_cron'])) {
					$arr_maybe = array("off", "five_min", "hourly", "six_hours", "twicedaily", "daily", "week");
					$brpv_run_cron = sanitize_text_field($_POST['brpv_run_cron']);
				
					if (in_array($brpv_run_cron, $arr_maybe)) {		
						brpv_optionUPD('brpv_status_cron', $brpv_run_cron, $feed_id, 'yes', 'set_arr');
						if ($brpv_run_cron === 'off') {
							// отключаем крон
							wp_clear_scheduled_hook('brpv_cron_period', array($feed_id));
							brpv_optionUPD('brpv_status_cron', 'off', $feed_id, 'yes', 'set_arr');
						
							wp_clear_scheduled_hook('brpv_cron_sborki', array($feed_id));
							brpv_optionUPD('brpv_status_sborki', '-1', $feed_id);
						} else {
							$recurrence = $brpv_run_cron;
							wp_clear_scheduled_hook('brpv_cron_period', array($feed_id));
							wp_schedule_event(time(), $recurrence, 'brpv_cron_period', array($feed_id));
							new BRPV_Error_Log('FEED № '.$feed_id.'; brpv_cron_period внесен в список заданий; Файл: export.php; Строка: '.__LINE__);
						}
					} else {
						new BRPV_Error_Log('Крон '.$brpv_run_cron.' не зарегистрирован. Файл: export.php; Строка: '.__LINE__);
					}
				}

				if (isset($_GET['tab']) && $_GET['tab'] === 'tags') {
					if (isset($_POST['brpv_params_arr'])) {
						brpv_optionUPD('brpv_params_arr', serialize($_POST['brpv_params_arr']), $feed_id);
					} else {brpv_optionUPD('brpv_params_arr', serialize(array()), $feed_id);}

					if (isset($_POST['brpv_add_in_name_arr'])) {
						brpv_optionUPD('brpv_add_in_name_arr', serialize($_POST['brpv_add_in_name_arr']), $feed_id);
					} else {brpv_optionUPD('brpv_add_in_name_arr', serialize(array()), $feed_id);}

					if (isset($_POST['brpv_no_group_id_arr'])) {
						brpv_optionUPD('brpv_no_group_id_arr', serialize($_POST['brpv_no_group_id_arr']), $feed_id);
					} else {brpv_optionUPD('brpv_no_group_id_arr', serialize(array()), $feed_id);}
				}

				$def_plugin_date_arr = new BRPV_Data_Arr();
				$opts_name_and_def_date_arr = $def_plugin_date_arr->get_opts_name_and_def_date('public');
				foreach ($opts_name_and_def_date_arr as $key => $value) {
					$save_if_empty = false;
					switch ($key) {
						case 'brpv_status_cron': 
						case 'brpvp_exclude_cat_arr': // селект категорий в прошке
							continue 2;
							break;
						case 'brpv_var_desc_priority':
						case 'brpv_one_variable':
						case 'brpv_skip_missing_products':
						case 'brpv_skip_backorders_products':
						case 'brpv_no_default_png_products':
						/* И галки в прошке */
						case 'brpvp_use_del_vc':
						case 'brpvp_excl_thumb':
						case 'brpvp_use_utm':
							if (!isset($_GET['tab']) || ($_GET['tab'] !== 'filtration')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;
						case 'brpv_pickup_options':
						case 'brpv_delivery_options':
						case 'brpv_delivery_options2':
							if (!isset($_GET['tab']) || ($_GET['tab'] !== 'tags')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;							
						case 'brpv_ufup':
							if (isset($_GET['tab']) && ($_GET['tab'] !== 'main_tab')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;
					}
					$this->save_plugin_set($key, $feed_id, $save_if_empty);
				}

			}
		} 

		$this->feed_id = $feed_id;
		return;
	}
}