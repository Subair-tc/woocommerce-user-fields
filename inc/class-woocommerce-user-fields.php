<?php

class WoocommerceUserFields {
    function __constructor(){

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


    public function print_user_frontend_fields() {
        $fields = $this->get_account_fields();
    
        foreach ( $fields as $key => $field_args ) {
            
            if ( is_user_logged_in() ) {
                $user_id = get_current_user_id();
                $value   = get_user_meta( $user_id, $key, true );
            }
    
            $value = isset( $field_args['value'] ) ? $field_args['value'] : $value;
            woocommerce_form_field( $key, $field_args, $value );
        }
    }


    public function add_checkout_fields() {
         $fields =  $this->get_account_fields();
 
        foreach ( $fields as $key => $field_args ) {
            $checkout_fields['account'][ $key ] = $field_args;
        }
    
        return $checkout_fields;
    }

    public function save_account_fields( $customer_id ) {
        $fields =  $this->get_account_fields();
        foreach ( $fields as $key => $field_args ) {
            $sanitize = isset( $field_args['sanitize'] ) ? $field_args['sanitize'] : 'wc_clean';
            $value    = isset( $_POST[ $key ] ) ? call_user_func( $sanitize, $_POST[ $key ] ) : '';
            update_user_meta( $customer_id, $key, $value );
        }
    }


    public  function validate_user_frontend_fields( $errors ) {
        $fields = $this->get_account_fields();
    
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


}