<?php
/*
Plugin Name: WooCommerce Cruise Charter
Plugin URI: https://github.com/joshuairl/woocommerce-cruise
Description: Allows users to book cruises.
Version: 0.0.1
Author: Joshua F. Rountree
Author URI: http://joshuairl.com
Licence : GPL
*/

function wc_cruise_init() {
    // Check if WooCommerce is active
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        include_once('wc-cruise-plugin.php');
        include_once('wc-cruise-cart-actions.php');
        include_once('options/wc-cruise-options.php');
    }
}

add_action('plugins_loaded', 'wc_cruise_init', 10);

function add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=wc_cruise_options">'.__('Settings','wc_cruise').'</a>';
    array_push( $links, $settings_link );
    return $links;
}

// add settings link
$plugin = plugin_basename( __FILE__ );
add_filter( 'plugin_action_links_' . $plugin, 'add_settings_link');

load_plugin_textdomain('wc_cruise', false, basename(dirname(__FILE__)).'/languages/');
