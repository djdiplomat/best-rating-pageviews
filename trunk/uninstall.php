<?php if (!defined('WP_UNINSTALL_PLUGIN')) {exit;}
if (is_multisite()) {
	delete_blog_option(get_current_blog_id(), 'brpv_version');
	delete_blog_option(get_current_blog_id(), 'brpv_posts_type_arr');
	delete_blog_option(get_current_blog_id(), 'brpv_not_count_bots');
	delete_blog_option(get_current_blog_id(), 'brpv_rating_icons');
} else {
	delete_option('brpv_version');
	delete_option('brpv_posts_type_arr');
	delete_option('brpv_not_count_bots');
	delete_option('brpv_rating_icons');
}
?>