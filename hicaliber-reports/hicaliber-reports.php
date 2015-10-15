<?php
/*
Plugin Name: Hicaliber Reports
Plugin URI: 
Description: Hicaliber Reporting Tool
Version: 1.0
Author: jeffreymoya
Author URI: https://linkedin.com/in/jeffreymoya
*/

include 'includes/report-controller.php';

if(is_admin())
{
    //handle amac reports - output pdf or xls / print page
    if(isset($_GET['export']))
    {
        add_action('admin_init', 'hc_export_report');
    }

    add_action('admin_menu', 'hc_add_report_admin_menu' );
}

function hc_reports_home($url='')
{
	return plugins_url($url, __FILE__);
}

function hc_add_report_admin_menu()
{
	add_menu_page(
	        'Reports',
	        'Reports',
	        'manage_options',
	        'amac-reports',
	        'hc_init_report_controller',
            null,
            "13.6"
	    );
}

function hc_init_report_controller()
{
	$reports = new ReportController;
    $reports->generate_report();
}

function hc_export_report()
{
	$reports = new ReportController;
    $reports->export();
}