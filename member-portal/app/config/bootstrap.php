<?php
MvcConfiguration::set(array(
    'Debug' => false
));

add_action('mvc_public_init', 'assets_public_init', 10, 1);
function assets_public_init($args) {
	extract($args);
	if ($controller == 'members') {
		if (in_array($action, array('index', 'login'))) {
            wp_enqueue_script('member-portal', plugins_url('../public/js/profile.js', __FILE__), array('jquery'), null, true);
            wp_enqueue_script('member-portal-foundation', get_template_directory_uri().'/assets/bower_components/foundation/js/foundation.min.js', null, null, true);
            wp_enqueue_script('member-portal-tabs', get_template_directory_uri().'/assets/bower_components/foundation/js/foundation/foundation.tab.js', array('member-portal-foundation'), null, true);
        }
    }
    wp_enqueue_style('member-portal', plugins_url('../public/css/mp.css', __FILE__));
}

add_action('mvc_admin_init', 'add_admin_css');
function add_admin_css() {
    wp_register_style( 'mp-admin-stylesheet', plugins_url( '../public/css/admin/mp-admin.css', __FILE__ ) );
	wp_enqueue_script('member-portal-admin-addons', plugins_url( '../public/js/admin/mp-admin.js', __FILE__ ), array('jquery'), null, true);
    wp_enqueue_style( 'mp-admin-stylesheet' );
}

add_action( 'login_form_middle', 'add_lost_password_link' );
function add_lost_password_link() {
    return '<a class="login-reset-link" href="'.home_url('/members/login?action=reset').'">Lost Password?</a>';
}

add_action( 'wp_login_failed', 'custom_login_failed', 10, 2 );
function custom_login_failed( $username )
{
    $referrer = wp_get_referer();

    $custom_login_url = home_url('/members/login');

    if ( $referrer && ! strstr($referrer, 'wp-login') && ! strstr($referrer, 'wp-admin') )
    {
        if ( empty($_GET['loggedout']) )
        wp_redirect( add_query_arg('action', 'failed', $custom_login_url) );
        else
        wp_redirect( add_query_arg('action', 'loggedout', $custom_login_url) );
        exit;
    }
}

//hide dashboard top menu
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    //if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    //}
}

add_filter('manage_users_columns', 'remove_posts_column', 11, 1);
function remove_posts_column($columns) 
{
  unset($columns['posts']);

  return $columns;
}

add_filter('editable_roles', 'exclude_other_roles');
function exclude_other_roles($roles) 
{
    if (isset($roles['editor'])) 
    {
      unset($roles['editor']);
    }
    if (isset($roles['contributor'])) 
    {
      unset($roles['contributor']);
    }
    if (isset($roles['subscriber'])) 
    {
      unset($roles['subscriber']);
    }
    if (isset($roles['author'])) 
    {
      unset($roles['author']);
    }

    return $roles;
}
