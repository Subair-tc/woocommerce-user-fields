
<?php
/*
Plugin Name: woo addon

*/


/* Set constant path to the plugin directory. */
define( 'WOO_ADDON_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_ADDON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
/* Set the constant path to the plugin's includes directory. */
define( 'WOO_ADDON_PLUGIN_INC', WOO_ADDON_PLUGIN_PATH . trailingslashit( 'inc' ), true );



//including class files
include_once( WOO_ADDON_PLUGIN_INC . 'class-woocommerce-user-fields.php' );
//include_once( WOO_ADDON_PLUGIN_INC . 'class-woocommerce-settings-tab.php' );