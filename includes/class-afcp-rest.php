<?php

class AFCP_Rest
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'custom_routes']);
    }

    // Регистрирует маршрут

    public function custom_routes()
    {
        register_rest_route(
            'afcp/v1',
            '/add',
            [
                'methods'  => ['POST'],
                'callback' => [$this, 'callback_rest'],
            ]
        );
    }




    public function callback_rest(WP_REST_Request $request)
    {

        error_log(print_r($request, 1));
        $thumb = $request->get_file_params();
        $this->validation();
        $this->validation_thumbnail($request->get_file_params());

        $event_data = [
            'post_type' => 'event',
            'post_status' => 'publish',
            'post_title' => sanitize_text_field($request->get_param('event_title')),
            'post_content' => wp_kses_post($request->get_param('event_descriptions')),
            'meta_input' => [
                'event_date' => sanitize_text_field($request->get_param('event_date')),
                'event_location' => sanitize_text_field($request->get_param('event_location')),
            ],
            'tax_input' => [
                'topics' => $request->get_param('event_topics'),
                'hashtags' => explode(',', sanitize_text_field($request->get_param('event_hashtags')))
            ]
        ];

        error_log(print_r($event_data, 1));
        $post_id = wp_insert_post($event_data);

        $this->set_term($post_id, $event_data['tax_input']);
        $this->upload_thumbnail($post_id, $thumb);

        $this->success('Event ' . $post_id . ' created successfully.');

        return [
            'message' => 'Event ' . $post_id . ' created successfully.'
        ];
    }

    public function upload_thumbnail($post_id, $thumb)
    {

        if (empty($thumb)) {
            return;
        }

        // все ок! Продолжаем.
        // Эти файлы должны быть подключены в лицевой части (фронт-энде).
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        //Format Validation 

        add_filter('upload_mimes', function () {
            return [
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png'          => 'image/png'
            ];
        });

        // Позволим WordPress перехватить загрузку.
        // не забываем указать атрибут name поля input - 'my_image_upload'
        $attachment_id = media_handle_upload('event_thumbnail', $post_id);

        if (is_wp_error($attachment_id)) {
            $response_message = 'Error `' . $_FILES['event_thumbnail']['name'] . '`: ' . $attachment_id->get_error_message();
            $this->error($response_message);
        }

        set_post_thumbnail($post_id, $attachment_id);

        // return $attachment_id;
    }

    public function set_term($post_id, $data)
    {
        foreach ($data as $key => $value) {
            wp_set_object_terms($post_id, $value, $key);
        }
    }

    public function validation()
    {
        $error = [];

        $required = [
            'event_title'        => 'This is required field',
            'event_topics'       => 'This is required field',
            // 'event_hashtags'     => 'This is required field',
            // 'event_descriptions' => 'This is required field',
            // 'event_thumbnail'    => 'This is required field',
            'event_date'         => 'This is required field',
            'event_location'     => 'This is required field'
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

    public function validation_thumbnail($file)
    {

        $size = getimagesize($file['event_thumbnail']['tmp_name']);
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
