
<?php
/*
Plugin Name: woo addon
Plugin URI: https://github.com/Subair-tc/woocommerce-user-fields
Description: wordpress plugin for woocommerce user account additional fields
Version: 0.1.0
Author: SUBAIR
Author URI: https://github.com/Subair-tc
Text Domain: woo-addon
*/

define( 'WOO_ADDON_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_ADDON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'WOO_ADDON_PLUGIN_INC', WOO_ADDON_PLUGIN_PATH . trailingslashit( 'inc' ), true );



add_filter( 'plugin_action_links_' . plugin_basename(__FILE__),  'wc_user_fields_add_action_links' );
function wc_user_fields_add_action_links ( $links ) {
        $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page=class-woocommerce-user-fields.php' ) . '">Settings</a>',
        );
        return array_merge( $links, $mylinks );
    }



//including class files
include_once( WOO_ADDON_PLUGIN_INC . 'class-woocommerce-user-fields.php' );
//include_once( WOO_ADDON_PLUGIN_INC . 'class-woocommerce-settings-tab.php' );