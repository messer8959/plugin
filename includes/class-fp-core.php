<?php

class FP_Core
{

    private static $instance;

    public function __construct()
    {
        $this->hooks();

        $this->includes();
    }

    public function hooks()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);

        add_action('init', [$this, 'register_cpt_event']);
        add_action('init', [$this, 'register_tax_topics']);
        add_action('init', [$this, 'register_tax_hashtags']);
    }

    public function includes()
    {
        require_once FP_DIR . 'includes/class-fp-shortcode.php';
        new FP_Shortcode();
    }

    public function enqueue()
    {
        wp_register_style(
            'fp_styles',
            FP_URI . 'assets/fp-styles.css',
            [],
            // filemtime(FP_DIR . 'assets/fp-style.css')
        );
        

        wp_register_script(
            'fp_scripts',
            FP_URI . 'assets/fp-scripts.js',
            [],
            // filemtime(FP_DIR . 'assets/fp-scripts.js'),
            true
        );

       

        wp_register_style(
            'fp_select2_style',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            [],
            null
        );
        wp_register_script(
            'fp_select2_script',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ['jquery', 'fp_scripts'],
            null,
            true
        );
    }

    public function register_cpt_event()
    {

        $labels = [
            'name'               => 'Події', // Основное название типа записи
            'singular_name'      => 'Подія', // отдельное название записи типа Book
            'add_new'            => 'Добавить новую подію',
            'add_new_item'       => 'Добавить новую подію',
            'edit_item'          => 'Редактировать подію',
            'new_item'           => 'Новая подія',
            'view_item'          => 'Посмотреть подію',
            'search_items'       => 'Найти подію',
            'not_found'          => 'Подій не найдено',
            'not_found_in_trash' => 'В корзине Подій не найдено',
            'parent_item_colon'  => '',
            'menu_name'          => 'Події'

        ];
        $args = [
            'labels'             => $labels,
            'public'             => true,
            'taxonomies'         => ['topics'],
            'query_var'          => true,
            'show_in_rest'       => true,
            'rewrite'            => true,
            'has_archive'        => true,
            'menu_position'      => 4,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields')
        ];
        register_post_type('event', $args);
    }

    public function register_tax_topics()
    {


        $args = [
            'hierarchical'          => true,
            'labels'                => [
                'name'              => 'Категорії',
                'singular_name'     => 'Категорія',
                'menu_name'         => 'Категорії'
            ],
            'public'                => true,
            'rewrite'               => [],
            'query_var'             => true,
            'show_ui'               => true
        ];

        register_taxonomy('topics', ['event'], $args);
    }

    public function register_tax_hashtags()
    {


        $args = [
            'hierarchical'          => false,
            'labels'                => [
                'name'              => 'Мітки',
                'singular_name'     => 'Мітка',
                'menu_name'         => 'Мітки'
            ],
            'public'                => true,
            'rewrite'               => [],
            'query_var'             => true,
            'show_ui'               => true
        ];

        register_taxonomy('hashtags', ['event'], $args);
    }


    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
