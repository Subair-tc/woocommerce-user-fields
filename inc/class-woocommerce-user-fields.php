<?php

class WoocommerceUserFields {
    public static function init() {
        
        add_action( 'woocommerce_register_form', __CLASS__ . '::print_user_frontend_fields', 10 ); // my account register
        add_action( 'woocommerce_edit_account_form', __CLASS__ . '::print_user_frontend_fields', 10 ); // my accoutn edit account
        add_filter( 'woocommerce_checkout_fields', __CLASS__ . '::add_checkout_fields', 10, 1 ); // registration from checkout

        add_action( 'show_user_profile', __CLASS__ . '::print_user_admin_fields', 30 ); // admin: edit profile
        add_action( 'edit_user_profile', __CLASS__ . '::print_user_admin_fields', 30 ); // admin: edit other users

        add_action( 'woocommerce_created_customer', __CLASS__ . '::save_account_fields' ); // register/checkout
        add_action( 'woocommerce_save_account_details', __CLASS__ . '::save_account_fields' ); // edit WC account
        add_action( 'personal_options_update', __CLASS__ . '::save_account_fields' ); // edit own account admin
        add_action( 'edit_user_profile_update', __CLASS__ . '::save_account_fields' );//edit others profile

        add_filter( 'woocommerce_registration_errors',  __CLASS__ . '::validate_user_frontend_fields', 10 ); // validation
        add_filter( 'woocommerce_save_account_details_errors',  __CLASS__ . '::validate_user_frontend_fields', 10 ); // update data

        add_action('admin_menu',  __CLASS__ . '::wc_user_fields_options_page'); // create option page

        add_action( 'admin_enqueue_scripts', __CLASS__ . '::add_wc_user_fields_style' ); // enqueing required styles and scripts
    }
    

    // get the extra field added
    private function get_account_fields() {
        $account_field_options = get_option("account_fields_admin_options");
        if( ! empty( $account_field_options ) ) {
             return $account_field_options;
        }
        return false;
    }

    // Get user id for editting profle data.
    private function get_edit_user_id() {
        return isset( $_GET['user_id'] ) ? (int) $_GET['user_id'] : get_current_user_id();
    }

    // Add Required styles and scripts
    public static function add_wc_user_fields_style() {
	
        wp_register_style( 'bootstrap-min-css', WOO_ADDON_PLUGIN_URL.'/css/bootstrap.min.css' );
        wp_enqueue_style( 'bootstrap-min-css' );

        wp_register_style( 'user-fields-custom-css', WOO_ADDON_PLUGIN_URL.'/css/custom.css' );
        wp_enqueue_style( 'user-fields-custom-css' );
        
        wp_register_script( 'bootstrap-min-js', WOO_ADDON_PLUGIN_URL.'/js/bootstrap.min.js', true );
        wp_enqueue_script( 'bootstrap-min-js' );
         wp_register_script( 'user-fields-custom-js', WOO_ADDON_PLUGIN_URL.'/js/custom.js', true );
        wp_enqueue_script( 'user-fields-custom-js' );
        
        wp_localize_script('user-fields-custom-js', 'Ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ));
    }

    // Print the Fields on different sections.
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

    // Added fileds into checkout page.
    public function add_checkout_fields() {
         $fields =  self::get_account_fields();
 
        foreach ( $fields as $key => $field_args ) {
            $checkout_fields['account'][ $key ] = $field_args;
        }
    
        return $checkout_fields;
    }

    // Add fields into admin sections.
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

    // Save field values.
    public function save_account_fields( $customer_id ) {
        $fields =  self::get_account_fields();
        foreach ( $fields as $key => $field_args ) {
            $sanitize = isset( $field_args['sanitize'] ) ? $field_args['sanitize'] : 'wc_clean';
            $value    = isset( $_POST[ $key ] ) ? call_user_func( $sanitize, $_POST[ $key ] ) : '';
            update_user_meta( $customer_id, $key, $value );
        }
    }

    // Validate form values for Required.
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

    //Add option page for adding new fields.
    public static function wc_user_fields_options_page() {
        if ( function_exists('add_options_page') ) {
            add_options_page('WC User Fields', 'WC User Fields', 'manage_options', basename(__FILE__), __CLASS__ . '::wc_user_fields_options');
        }
    }

    // Admin page UI and validations.
    public function wc_user_fields_options(){
        ?>

            <div class="container">
                <div class="wc-uf-contaner">
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewField">
                                Add New Field
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                                <table class="table table-striped table-dark">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Type</th> 
                                            <th scope="col">Label</th>                                                          
                                            <th scope="col">Required</th>
                                            <th scope="col">edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php
                                    $accoutn_field_options = self::get_account_fields();
                                    //var_dump($accoutn_field_options);
                                    $i = 0;
                                    foreach ( $accoutn_field_options as $field=>$filed_array ) {
                                        //var_dump($field);
                                        //var_dump($filed_array);
                                        ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><?php echo $field; ?></td>
                                                <td><?php echo $filed_array['type']; ?></td>
                                                <td><?php echo $filed_array['label']; ?></td>
                                                <td><?php echo $filed_array['required']; ?></td>
                                                <td> <span data-toggle="modal" data-target="#addNewField" data-field= <?php echo $field; ?>  data-label= <?php echo $filed_array['label']; ?> data-type= <?php echo $filed_array['type']; ?> data-required= <?php echo $filed_array['required']; ?> >edit</span></td>
                                            </tr>
                                        <?php
                                    }
                                ?>
                                    <tbody>
                                </table>
                        </div>
                    </div>

                    
                </div>

            </div>

          <div class="modal fade" id="addNewField" tabindex="-1" role="dialog" aria-labelledby="addNewFieldLabel" aria-hidden="true">
            
            <form name="update_user_fields" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
            <?php
                if ( function_exists('wp_nonce_field') ) {
                    wp_nonce_field('update_user_fields-options');
                }	
            ?>
            
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="addNewFieldLabel">Add New Field</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>please use same field name for overwrite the existing field.</p>
                        <?php
                            if( isset($_POST['save_fields'])) {
                                check_admin_referer('update_user_fields-options');
                                $field_label        = isset( $_POST['field_label'] ) ? $_POST['field_label'] : '';
                                $field_name         = isset( $_POST['field_name'] ) ? $_POST['field_name'] : '';
                                $field_type         = isset( $_POST['field_type'] ) ? $_POST['field_type'] : '';
                                $field_value        = isset( $_POST['field_value'] ) ? $_POST['field_value'] : '';
                                $field_is_required  = isset( $_POST['field_is_required'] ) ? true : false;

                                if( $field_label == '' || $field_name =='' || $field_type=='' || ( $field_type == 'dropdown' && $field_value =='' )  ) {
                                    echo '<div class="danger_block alert alert-danger alert-dismissable">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                        Please fill all required fields.
                                        </div>';
                                } else {
                                    $accoutn_field_options = self::get_account_fields();

                                   

                                    $new_field_details = array(
                                        'type'        =>  $field_type ,
                                        'label'       => __( $field_label , 'woo-addon' ),
                                        'placeholder' => __( $field_label , 'woo-addon' ),
                                        'sanitize'    => 'wc_clean',
                                        'required'    => $field_is_required,
                                    );

                                     if( $field_type == 'select') {
                                        
                                        $options_array = array(
                                            __( 'Select an option...', 'woo-addon'  ),
                                        );

                                        $options = explode(',',$field_value);
                                        foreach( $options as $option ) {
                                            array_push($options_array , $option );
                                        }

                                        $new_field_details['options'] = $options_array;
                                    }

                                    $accoutn_field_options[ $field_name ] = $new_field_details;

                                    update_option("account_fields_admin_options", $accoutn_field_options);
                                    echo '<div class="success_block alert alert-success alert-dismissable">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                            Options updated successfully.
                                        </div>';

                                   // var_dump( $new_field_details);
                                }



                            }


                        ?>

                            <div class="item-block">
                                <div class="form-group">
                                    <label for="field_label">Field Label <span>*</span></label>
                                    <input  type="text"  class="form-control" id="field_label" name="field_label" placeholder="Field Label" />
                                </div>

                                <div class="form-group">
                                    <label for="field_name">Field Name<span>*</span></label>
                                    <input type="text"  class="form-control" id="field_name" name="field_name" placeholder="Field Name" />
                                </div>

                                <div class="form-group">
                                    <label for="field_type">Field Type<span>*</span></label>
                                    <select  id="field_type"  name="field_type"  class="form-control" >
                                        <option value="" > Select field type </option>
                                        <option value="text" > text </option>
                                        <option value="select" >dropdown</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="field_value">Field Value</label>
                                    <input type="text"  class="form-control" id="field_value"  name="field_value"  placeholder="Field Value" />
                                    <span> for drop down type add options as values with comma seperated</span>
                                </div>

                                <div class="form-group">
                                    <label for="field_is_required">Is Required?</label>
                                    <input type="checkbox"  class="form-control"  name="field_is_required" />
                                </div>
                                

                            </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit"  class="btn btn-primary" value="Save fields" name="save_fields"/>
                    </div>
                </div>
            </div>
            </form>
            </div>


        <?php
    }




}

WoocommerceUserFields::init();