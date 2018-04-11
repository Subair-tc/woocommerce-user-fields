<?php

class WoocommerceUserFields {
    public static function init() {
        
        add_action( 'woocommerce_register_form', __CLASS__ . '::print_user_frontend_fields', 10 );
        add_action( 'woocommerce_edit_account_form', __CLASS__ . '::print_user_frontend_fields', 10 );
        add_filter( 'woocommerce_checkout_fields', __CLASS__ . '::add_checkout_fields', 10, 1 );

        add_action( 'show_user_profile', __CLASS__ . '::print_user_admin_fields', 30 ); // admin: edit profile
        add_action( 'edit_user_profile', __CLASS__ . '::print_user_admin_fields', 30 ); // admin: edit other users

        add_action( 'woocommerce_created_customer', __CLASS__ . '::save_account_fields' ); // register/checkout
        add_action( 'woocommerce_save_account_details', __CLASS__ . '::save_account_fields' ); // edit WC account
        add_action( 'personal_options_update', __CLASS__ . '::save_account_fields' ); // edit own account admin
        add_action( 'edit_user_profile_update', __CLASS__ . '::save_account_fields' );//edit others profile

        add_filter( 'woocommerce_registration_errors',  __CLASS__ . '::validate_user_frontend_fields', 10 );
        add_filter( 'woocommerce_save_account_details_errors',  __CLASS__ . '::validate_user_frontend_fields', 10 );

        add_action('admin_menu',  __CLASS__ . '::wc_user_fields_options_page');


    }

    private function get_account_fields() {
        return apply_filters( 'account_fields', array(
            'website' => array(
                'type'        => 'text',
                'label'       => __( 'Website', 'woo-addon' ),
                'placeholder' => __( 'Webste', 'woo-addon' ),
                'sanitize'    => 'wc_clean',
                'required'    => true,
                
            ),
        ) );
    }

    private function get_edit_user_id() {
        return isset( $_GET['user_id'] ) ? (int) $_GET['user_id'] : get_current_user_id();
    }

    public function print_user_frontend_fields() {
        $fields = self::get_account_fields();
    
        foreach ( $fields as $key => $field_args ) {
            
            if ( is_user_logged_in() ) {
                $user_id = self::get_edit_user_id();
                $value   = get_user_meta( $user_id, $key, true );
            }
    
            $value = isset( $field_args['value'] ) ? $field_args['value'] : $value;
            woocommerce_form_field( $key, $field_args, $value );
        }
    }


    public function add_checkout_fields() {
         $fields =  self::get_account_fields();
 
        foreach ( $fields as $key => $field_args ) {
            $checkout_fields['account'][ $key ] = $field_args;
        }
    
        return $checkout_fields;
    }


    function print_user_admin_fields() {
        $fields =  self::get_account_fields();
        ?>
        <h2><?php _e( 'Additional Information', 'woo-addon' ); ?></h2>
        <table class="form-table" id="woo-addon-additional-information">
            <tbody>
            <?php foreach ( $fields as $key => $field_args ) { ?>
                <?php
                $user_id = self::get_edit_user_id();;
                $value   = get_user_meta( $user_id, $key, true );
                ?>
                <tr>
                    <th>
                        <label for="<?php echo $key; ?>"><?php echo $field_args['label']; ?></label>
                    </th>
                    <td>
                        <?php $field_args['label'] = false; ?>
                        <?php woocommerce_form_field( $key, $field_args, $value ); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php
    }


    public function save_account_fields( $customer_id ) {
        $fields =  self::get_account_fields();
        foreach ( $fields as $key => $field_args ) {
            $sanitize = isset( $field_args['sanitize'] ) ? $field_args['sanitize'] : 'wc_clean';
            $value    = isset( $_POST[ $key ] ) ? call_user_func( $sanitize, $_POST[ $key ] ) : '';
            update_user_meta( $customer_id, $key, $value );
        }
    }


    public  function validate_user_frontend_fields( $errors ) {
        $fields = self::get_account_fields();
    
        foreach ( $fields as $key => $field_args ) {
            if ( empty( $field_args['required'] ) ) {
                continue;
            }

            if ( empty( $_POST[ $key ] ) ) {
                $message = sprintf( __( '%s is a required field.', 'iconic' ), '<strong>' . $field_args['label'] . '</strong>' );
                $errors->add( $key, $message );
            }
        }
    
        return $errors;
    }


    public static function wc_user_fields_options_page() {
        if ( function_exists('add_options_page') ) {
            add_options_page('WC User Fields', 'WC User Fields', 'manage_options', basename(__FILE__), __CLASS__ . '::wc_user_fields_options');
        }
    }

    public function wc_user_fields_options(){
        ?>

            <div class="wc-uf-contaner">
                <form name="update_user_fields" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">

                    <div class="item-block">
                        <div vlass="form-group">
                            <label for="field_label">Field Label</label>
                            <input type="text" name="field_label" placeholder="Field Label" />
                        </div>

                        <div vlass="form-group">
                            <label for="field_name">Field Name</label>
                            <input type="text" name="field_name" placeholder="Field Name" />
                        </div>

                        <div vlass="form-group">
                            <label for="field_type">Field Type</label>
                            <select  name="field_type">
                                <option value="text" >
                                <option value="dropdown" >
                            </select>
                        </div>

                         <div vlass="form-group">
                            <label for="field_value">Field Value</label>
                            <input type="text" name="field_value" placeholder="Field Value" />
                            <span> for drop down type addoptions as values with comma seperated</span>
                         </div>

                         <div vlass="form-group">
                            <label for="field_is_required">Is Required?</label>
                            <input type="checkbox" name="field_is_required" />
                         </div>

                         <div vlass="form-group">
                            <button name="add-new" id="add-new-block">Add New</button>
                         </div>

                         <div vlass="form-group">
                            <input type="submit" value="save fields" name="save_fields"/>

                         </div>
                        

                    </div>


                </form>

            </div>


        <?php
    }




}

WoocommerceUserFields::init();