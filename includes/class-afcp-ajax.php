<?php

class AFCP_Ajax{
    public function __construct(){
        add_action( 'wp_ajax_created_event', [ $this, 'callback' ] );
		add_action( 'wp_ajax_nopriv_created_event', [ $this, 'callback' ] );
    }

    public function callback(){
        error_log( print_r( $_POST, 1) );

        check_ajax_referer( 'afcp-ajax-nonce', 'nonce' );

        $this->validation();


        wp_die();
    }

    public function validation(){
        $error = [];

        $required = [
            'event_title'        => 'This is required field',
            // 'event_topics'       => 'This is required field',
            // 'event_hashtags'     => 'This is required field',
            // 'event_descriptions' => 'This is required field',
            // 'event_thumbnail'    => 'This is required field',
            // 'event_date'         => 'This is required field',
            // 'event_location'     => 'This is required field'
        ];

        foreach( $required as $key => $item ) {
            if( empty( $_POST[$key] ) || ! isset( $_POST[$key] ) ){
                $error[ $key ] = $item;
            }
        }

        if( $error ){
            $this->error($error );
        }
    }

    public function success($message){
        
        wp_send_json_success([
            'response' => 'SUCCESS',
            'message' => $message
        ]);

    }

    public function error($message){
        
        wp_send_json_error([
            'response' => 'ERROR',
            'message' => $message
        ]);

    }
}