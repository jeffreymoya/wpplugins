<?php
/*
Plugin Name: AMAC Members Portal
Plugin URI:  
Description: Membership management for AMAC.
Version:     1.0
Author:      hicaliber
Author URI:  http://hicaliber.com.au/
License:     GPLv2
*/

register_activation_hook(__FILE__, 'member_portal_activate');
register_deactivation_hook(__FILE__, 'member_portal_deactivate');

function member_portal_activate() {
    require_once dirname(__FILE__).'/member_portal_loader.php';
    $loader = new MemberPortalLoader();
    $loader->activate();
}

function member_portal_deactivate() {
    require_once dirname(__FILE__).'/member_portal_loader.php';
    $loader = new MemberPortalLoader();
    $loader->deactivate();
}

function amac_mp_home($url='')
{
	return plugins_url($url, __FILE__);
}

?>