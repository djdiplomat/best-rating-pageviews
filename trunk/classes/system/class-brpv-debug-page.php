<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Debug Page
*
* @link			https://icopydoc.ru/
* @since		2.1.0
*/

class BRPV_Debug_Page {
	private $pref = 'yfym';	
	private $feedback;

	public function __construct($pref = null) {
		if ($pref) {$this->pref = $pref;}
		$this->feedback = new BRPV_Feedback();

		$this->listen_submit();
		$this->get_html_form();	
	}

	public function get_html_form() { ?>
 		<div class="wrap">
			<h1><?php _e('Debug page', $this->get_pref()); ?> v.<?php echo yfym_optionGET('yfym_version'); ?></h1>
			<?php do_action('my_admin_notices'); ?>
			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_block_logs(); ?>
						</div>
					</div>
					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_block_simulation(); ?>
						</div>
					</div>
					<div id="postbox-container-3" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_block_possible_problems(); ?>
							<?php $this->get_html_block_sandbox(); ?>
						</div>
					</div>
					<div id="postbox-container-4" class="postbox-container">
						<div class="meta-box-sortables">
							<?php do_action('yfym_before_support_project'); ?>
							<?php $this->feedback->get_form(); ?>
						</div>
					</div>
				</div>
			</div>		
		</div><?php // end get_html_form();
	}

	public function get_html_block_logs() { 
		$yfym_keeplogs = yfym_optionGET($this->get_input_name_keeplogs());
		$yfym_disable_notices = yfym_optionGET($this->get_input_name_disable_notices());
		$yfym_enable_five_min = yfym_optionGET($this->get_input_name_enable_five_min()); ?>		    	 
		<div class="postbox">
			<h2 class="hndle"><?php _e('Logs', $this->get_pref()); ?></h2>
			<div class="inside">
				<p><?php if ($yfym_keeplogs === 'on') {
					$upload_dir = wp_get_upload_dir();
					echo '<strong>'. __("Log-file here", $this->get_pref()).':</strong><br /><a href="'.$upload_dir['baseurl'].'/yfym/plugin.log" target="_blank">'.$upload_dir['basedir'].'/yfym/plugin.log</a>';			
				} ?></p>
				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
					<table class="form-table"><tbody>
					<tr>
						<th scope="row"><label for="<?php echo $this->get_input_name_keeplogs(); ?>"><?php _e('Keep logs', $this->get_pref()); ?></label><br />
							<input class="button" id="<?php echo $this->get_submit_name_clear_logs(); ?>" type="submit" name="<?php echo $this->get_submit_name_clear_logs(); ?>" value="<?php _e('Clear logs', $this->get_pref()); ?>" />
						</th>
						<td class="overalldesc">
							<input type="checkbox" name="<?php echo $this->get_input_name_keeplogs(); ?>" id="<?php echo $this->get_input_name_keeplogs(); ?>" <?php checked($yfym_keeplogs, 'on' ); ?>/><br />
							<span class="description"><?php _e('Do not check this box if you are not a developer', $this->get_pref()); ?>!</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo $this->get_input_name_disable_notices(); ?>"><?php _e('Disable notices', $this->get_pref()); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="<?php echo $this->get_input_name_disable_notices(); ?>" id="<?php echo $this->get_input_name_disable_notices(); ?>" <?php checked($yfym_disable_notices, 'on'); ?>/><br />
							<span class="description"><?php _e('Disable notices about YML-construct', $this->get_pref()); ?>!</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo $this->get_input_name_enable_five_min(); ?>"><?php _e('Enable', $this->get_pref()); ?> five_min</label></th>
						<td class="overalldesc">
							<input type="checkbox" name="<?php echo $this->get_input_name_enable_five_min(); ?>" id="<?php echo $this->get_input_name_enable_five_min(); ?>" <?php checked($yfym_enable_five_min, 'on'); ?>/><br />
							<span class="description"><?php _e('Enable the five minute interval for CRON', $this->get_pref()); ?></span>
						</td>
					</tr>		 
					<tr>
						<th scope="row"><label for="button-primary"></label></th>
						<td class="overalldesc"></td>
					</tr>		 
					<tr>
						<th scope="row"><label for="button-primary"></label></th>
						<td class="overalldesc"><?php wp_nonce_field($this->get_nonce_action_debug_page(), $this->get_nonce_field_debug_page()); ?><input id="button-primary" class="button-primary" type="submit" name="<?php echo $this->get_submit_name(); ?>" value="<?php _e('Save', $this->get_pref()); ?>" /><br />
						<span class="description"><?php _e('Click to save the settings', $this->get_pref()); ?></span></td>
					</tr>         
					</tbody></table>
				</form>
			</div>
		</div><?php
	} // end get_html_block_logs();
	
	public function get_html_block_simulation() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Request simulation', $this->get_pref()); ?></h2>
			<div class="inside">
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
				<?php $resust_simulated = ''; $resust_report = '';
				if (isset($_POST['yfym_num_feed'])) {$yfym_num_feed = sanitize_text_field($_POST['yfym_num_feed']);} else {$yfym_num_feed = '1';} 
				if (isset($_POST['yfym_simulated_post_id'])) {$yfym_simulated_post_id = sanitize_text_field($_POST['yfym_simulated_post_id']);} else {$yfym_simulated_post_id = '';}
				if (isset($_REQUEST['yfym_submit_simulated'])) {
					if (!empty($_POST) && check_admin_referer('yfym_nonce_action_simulated', 'yfym_nonce_field_simulated')) {		 
						$post_id = (int)$yfym_simulated_post_id;

						$result_get_unit_obj = new BRPV_Get_Unit($post_id, $yfym_num_feed);
						$simulated_result_xml = $result_get_unit_obj->get_result();	

						$resust_report_arr = $result_get_unit_obj->get_skip_reasons_arr();

						if (empty($resust_report_arr)) {
							$resust_report = 'Всё штатно';
						} else {
							foreach ($result_get_unit_obj->get_skip_reasons_arr() as $value) {
								$resust_report .= $value .PHP_EOL;
							}					
						}
						$resust_simulated = $simulated_result_xml;
					}
				} ?>		
					<table class="form-table"><tbody>
						<tr>
							<th scope="row"><label for="yfym_simulated_post_id">postId</label></th>
							<td class="overalldesc">
								<input type="number" min="1" name="yfym_simulated_post_id" value="<?php echo $yfym_simulated_post_id; ?>">
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="yfym_enable_five_min">numFeed</label></th>
							<td class="overalldesc">
								<select style="width: 100%" name="yfym_num_feed" id="yfym_num_feed">
									<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';}
									$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
									$yfym_settings_arr_keys_arr = array_keys($yfym_settings_arr); 
									for ($i = 0; $i < count($yfym_settings_arr_keys_arr); $i++) :
										$numFeed = (string)$yfym_settings_arr_keys_arr[$i]; 
										if ($yfym_settings_arr[$numFeed]['yfym_feed_assignment'] === '') {
											$feed_assignment = '';
										} else {
											$feed_assignment = ' ('.$yfym_settings_arr[$numFeed]['yfym_feed_assignment'].')';
										} ?>
										<option value="<?php echo $numFeed; ?>" <?php selected($yfym_num_feed, $numFeed); ?>><?php _e('Feed', $this->get_pref()); ?> <?php echo $numFeed; ?>: feed-yml-<?php echo $cur_blog_id; ?>.xml<?php echo $feed_assignment; ?></option>
									<?php endfor; ?>
								</select>
							</td>
						</tr>	
						<tr>
							<th scope="row" colspan="2"><textarea rows="4" style="width: 100%;"><?php echo htmlspecialchars($resust_report); ?></textarea></th>
						</tr>
						<tr>
							<th scope="row" colspan="2"><textarea rows="16" style="width: 100%;"><?php echo htmlspecialchars($resust_simulated); ?></textarea></th>
						</tr>
					</tbody></table>
					<?php wp_nonce_field('yfym_nonce_action_simulated', 'yfym_nonce_field_simulated'); ?><input class="button-primary" type="submit" name="yfym_submit_simulated" value="<?php _e('Simulated', $this->get_pref()); ?>" />
				</form>			
			</div>
		</div><?php // end get_html_feeds_list();
	} // end get_html_block_simulation();

	public function get_html_block_possible_problems() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Possible problems', $this->get_pref()); ?></h2>
			<div class="inside"><?php
				$possible_problems_arr = $this->get_possible_problems_list();
				if ($possible_problems_arr[1] > 0) { // $possibleProblemsCount > 0) {
					echo '<ol>'.$possible_problems_arr[0].'</ol>';
				} else {
					echo '<p>'. __('Self-diagnosis functions did not reveal potential problems', $this->get_pref()).'.</p>';
				}
			?></div>
		</div><?php
	} // end get_html_block_sandbox();

	public function get_html_block_sandbox() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Sandbox', $this->get_pref()); ?></h2>
			<div class="inside"><?php
				require_once BRPV_PLUGIN_DIR_PATH.'/sandbox.php';
				try {
					yfym_run_sandbox();
				} catch (Exception $e) {
					echo 'Exception: ',  $e->getMessage(), "\n";
				}
			?></div>
	   </div><?php
	} // end get_html_block_sandbox();

	public static function get_possible_problems_list() {
		$possibleProblems = ''; $possibleProblemsCount = 0; $conflictWithPlugins = 0; $conflictWithPluginsList = ''; 
		$check_global_attr_count = wc_get_attribute_taxonomies();
		if (count($check_global_attr_count) < 1) {
			$possibleProblemsCount++;
			$possibleProblems .= '<li>'. __('Your site has no global attributes! This may affect the quality of the YML feed. This can also cause difficulties when setting up the plugin', 'yfym'). '. <a href="https://icopydoc.ru/globalnyj-i-lokalnyj-atributy-v-woocommerce/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=debug-page&utm_term=possible-problems">'. __('Please read the recommendations', 'yfym'). '</a>.</li>';
		}	
		if (is_plugin_active('snow-storm/snow-storm.php')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Snow Storm<br/>';
		}
		if (is_plugin_active('email-subscribers/email-subscribers.php')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Email Subscribers & Newsletters<br/>';
		}
		if (is_plugin_active('saphali-search-castom-filds/saphali-search-castom-filds.php')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Email Subscribers & Newsletters<br/>';
		}
		if (is_plugin_active('w3-total-cache/w3-total-cache.php')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'W3 Total Cache<br/>';
		}
		if (is_plugin_active('docket-cache/docket-cache.php')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Docket Cache<br/>';
		}					
		if (class_exists('MPSUM_Updates_Manager')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Easy Updates Manager<br/>';
		}
		if (class_exists('OS_Disable_WordPress_Updates')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Disable All WordPress Updates<br/>';
		}
		if ($conflictWithPlugins > 0) {
			$possibleProblemsCount++;
			$possibleProblems .= '<li><p>'. __('Most likely, these plugins negatively affect the operation of', 'yfym'). ' YML for Yandex Market:</p>'.$conflictWithPluginsList.'<p>'. __('If you are a developer of one of the plugins from the list above, please contact me', 'yfym').': <a href="mailto:support@icopydoc.ru">support@icopydoc.ru</a>.</p></li>';
		}
		return array($possibleProblems, $possibleProblemsCount, $conflictWithPlugins, $conflictWithPluginsList);
	}
	
	private function get_pref() {
		return $this->pref;
	}

	private function get_input_name_keeplogs() {
		return $this->get_pref().'_keeplogs';
	}

	private function get_input_name_disable_notices() {
		return $this->get_pref().'_disable_notices';
	}

	private function get_input_name_enable_five_min() {
		return $this->get_pref().'_enable_five_min';
	}

	private function get_submit_name() {
		return $this->get_pref().'_submit_debug_page';
	}

	private function get_nonce_action_debug_page() {
		return $this->get_pref().'_nonce_action_debug_page';
	}

	private function get_nonce_field_debug_page() {
		return $this->get_pref().'_nonce_field_debug_page';
	}

	private function get_submit_name_clear_logs() {
		return $this->get_pref().'_submit_clear_logs';
	}	

	private function listen_submit() {
		if (isset($_REQUEST[$this->get_submit_name()])) {
			$this->seve_data();
			$message = __('Updated', $this->get_pref());
			$class = 'notice-success';	

			add_action('my_admin_notices', function() use ($message, $class) { 
				$this->admin_notices_func($message, $class);
			}, 10, 2);
		}
		
		if (isset($_REQUEST[$this->get_submit_name_clear_logs()])) {
			$filename = BRPV_PLUGIN_UPLOADS_DIR_PATH.'/plugin.log';
			$res = unlink($filename);
			if ($res == true) {
				$message = __('Logs were cleared', $this->get_pref());
				$class = 'notice-success';				
			} else {
				$message = __('Error accessing log file. The log file may have been deleted previously', $this->get_pref());
				$class = 'notice-warning';	
			}

			add_action('my_admin_notices', function() use ($message, $class) { 
				$this->admin_notices_func($message, $class);
			}, 10, 2);
		}
		return;
	}

	private function seve_data() {
		if (!empty($_POST) && check_admin_referer($this->get_nonce_action_debug_page(), $this->get_nonce_field_debug_page())) { 
			if (isset($_POST[$this->get_input_name_keeplogs()])) {
				$keeplogs = sanitize_text_field( $_POST[$this->get_input_name_keeplogs()] );
			} else {
				$keeplogs = '';
			}
			if (isset($_POST[$this->get_input_name_disable_notices()])) {
				$disable_notices = sanitize_text_field( $_POST[$this->get_input_name_disable_notices()] );
			} else {
				$disable_notices = '';
			}
			if (isset($_POST[$this->get_input_name_enable_five_min()])) {
				$enable_five_min = sanitize_text_field( $_POST[$this->get_input_name_enable_five_min()] );
			} else {
				$enable_five_min = '';
			}
			if (is_multisite()) {
				update_blog_option(get_current_blog_id(), 'yfym_keeplogs', $keeplogs);
				update_blog_option(get_current_blog_id(), 'yfym_disable_notices', $disable_notices);
				update_blog_option(get_current_blog_id(), 'yfym_enable_five_min', $enable_five_min);
			} else {
				update_option('yfym_keeplogs', $keeplogs);
				update_option('yfym_disable_notices', $disable_notices);
				update_option('yfym_enable_five_min', $enable_five_min);
			}
		}
		return;
	}

	private function admin_notices_func($message, $class) {
		printf('<div class="notice %1$s"><p>%2$s</p></div>', $class, $message);
	}
}