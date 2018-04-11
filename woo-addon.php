
<?php
/*
Plugin Name: woo addon

*/


/* Set constant path to the plugin directory. */
define( 'WOO_ADDON_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_ADDON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
/* Set the constant path to the plugin's includes directory. */
define( 'WOO_ADDON_PLUGIN_INC', WOO_ADDON_PLUGIN_PATH . trailingslashit( 'inc' ), true );

function woo_addon_add_registration_fields(){
    $woo_object = new WoocommerceUserFields;
    $woo_object->print_user_frontend_fields();
}
 
add_action( 'woocommerce_register_form', 'woo_addon_add_registration_fields', 10 );
add_action( 'woocommerce_edit_account_form', 'woo_addon_add_registration_fields', 10 );

function woo_addon_add_checkout_fields( $checkout_fields ) {
    $woo_object = new WoocommerceUserFields;
    $woo_object->add_checkout_fields();
}
 
add_filter( 'woocommerce_checkout_fields', 'woo_addon_add_checkout_fields', 10, 1 );


function woo_addon_save_account_fields( $customer_id ) {
    $woo_object = new WoocommerceUserFields;
    $woo_object->save_account_fields( $customer_id );
}
 
add_action( 'woocommerce_created_customer', 'woo_addon_save_account_fields' ); // register/checkout
add_action( 'woocommerce_save_account_details', 'woo_addon_save_account_fields' ); // edit WC account




function woo_addon_validate_user_frontend_fields( $errors ) {
    $woo_object = new WoocommerceUserFields;
    return $woo_object->validate_user_frontend_fields( $errors );
}
 
add_filter( 'woocommerce_registration_errors', 'woo_addon_validate_user_frontend_fields', 10 );
add_filter( 'woocommerce_save_account_details_errors', 'woo_addon_validate_user_frontend_fields', 10 );




//including class files
include_once( WOO_ADDON_PLUGIN_INC . 'class-woocommerce-user-fields.php' );



/*add_action( 'register_form', 'myplugin_register_form' );
function myplugin_register_form() {

    $first_name = ( ! empty( $_POST['first_name'] ) ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        
        ?>
        <p>
            <label for="first_name"><?php _e( 'First Name', 'mydomain' ) ?><br />
                <input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(  $first_name  ); ?>" size="25" /></label>
        </p>
        <?php
    }
    add_filter( 'registration_errors', 'myplugin_registration_errors', 10, 3 );
    function myplugin_registration_errors( $errors, $sanitized_user_login, $user_email ) {
        
        if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
        $errors->add( 'first_name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must include a first name.', 'mydomain' ) ) );

        }

        return $errors;
    }
    add_action( 'user_register', 'myplugin_user_register' );
    function myplugin_user_register( $user_id ) {
        if ( ! empty( $_POST['first_name'] ) ) {
            update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['first_name'] ) );
        }
    }