<?php

class AFCP_Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_created_event', [$this, 'callback']);
        add_action('wp_ajax_nopriv_created_event', [$this, 'callback']);
    }

    public function callback()
    {
        error_log(print_r($_POST, 1));

        check_ajax_referer('afcp-ajax-nonce', 'nonce');

        $this->validation();

        $this->validation_thumbnail();

        $event_data = [
            'post_type' => 'event',
            'post_status' => 'publish',
            'post_title' => sanitize_text_field($_POST['event_title']),
            'post_content' => wp_kses_post($_POST['event_descriptions']),
            'meta_input' => [
                'event_date' => sanitize_text_field($_POST['event_date']),
                'event_location' => sanitize_text_field($_POST['event_location']),
            ],
            'tax_input' => [
                'topics' => $_POST['event_topics'],
                'hashtags' => explode(',', sanitize_text_field($_POST['event_hashtags']))
            ]
        ];

        error_log(print_r($event_data, 1));
        $post_id = wp_insert_post($event_data);

        $this->set_term($post_id, $event_data['tax_input']);

        wp_die();
    }

    public function set_term($post_id, $data){
        foreach($data as $key => $value ){
            wp_set_object_terms( $post_id,$value,$key );
        }
        
    }

    public function validation()
    {
        $error = [];

        $required = [
            // 'event_title'        => 'This is required field',
            // 'event_topics'       => 'This is required field',
            // 'event_hashtags'     => 'This is required field',
            // 'event_descriptions' => 'This is required field',
            // 'event_thumbnail'    => 'This is required field',
            // 'event_date'         => 'This is required field',
            // 'event_location'     => 'This is required field'
        ];

        foreach ($required as $key => $item) {
            if (empty($_POST[$key]) || ! isset($_POST[$key])) {
                $error[$key] = $item;
            }
        }

        if ($error) {
            $this->error($error);
        }
    }

    public function validation_thumbnail()
    {

        $size = getimagesize($_FILES['event_thumbnail']['tmp_name']);
        $max_size = 800;
        // $type = $_FILES['event_thumbnail']['type'];


        if ($size[0] > $max_size || $size[1] > $max_size) {

            unlink(($_FILES['event_thumbnail']['tmp_name']));

            $image_message = 'Unacceptable Imega Size';

            $this->error($image_message);
        }

        // if( 'image/jpeg' !== $type || 'image/png' !== $type ){

        //     unlink(($_FILES['event_thumbnail']['tmp_name']));

        //     $image_message = 'Unacceptable Image Format';

        //     $this->error($image_message);

        // }

    }

    public function success($message)
    {

        wp_send_json_success([
            'response' => 'SUCCESS',
            'message' => $message
        ]);
    }

    public function error($message)
    {

        wp_send_json_error([
            'response' => 'ERROR',
            'message' => $message
        ]);
    }
}
