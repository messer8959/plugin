<?php

class AFCP_Core
{

	private static $instanse;

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
		require_once AFCP_DIR . 'includes/class-afcp-shortcode.php';
		new AFCP_Shortcode();
		// require_once AFCP_DIR . 'includes/class-afcp-ajax.php';
		// new AFCP_Ajax();
		require_once AFCP_DIR . 'includes/class-afcp-rest.php';
		new AFCP_Rest();
	}

	public function enqueue()
	{

		wp_enqueue_script('jquery');

		wp_register_style(
			'afcp-styles',
			AFCP_URI . 'assets/fp-styles.css',
			[],
			filemtime(AFCP_DIR . 'assets/fp-styles.css')
		);



		wp_register_script(
			'afcp-script',
			AFCP_URI . 'assets/fp-script.js',
			['jquery'],
			filemtime(AFCP_DIR . 'assets/fp-script.js')
		);

		wp_register_script(
			'afcp-script',
			AFCP_URI . 'assets/fp-script.js',
			['jquery'],
			filemtime(AFCP_DIR . 'assets/fp-script.js'),
			true
		);

		// wp_register_script(
		//     'afcp-script-ajax', 
		//     AFCP_URI . 'assets/afcp-ajax.js',
		//     ['jquery'],
		//     filemtime(AFCP_DIR . 'assets/afcp-ajax.js'),
		// 	true
		// );

		// wp_localize_script(
		// 	'afcp-script-ajax',
		// 	'afcp_ajax',
		// 	[
		// 		'url'   => admin_url( 'admin-ajax.php' ),
		// 		'nonce' => wp_create_nonce( 'afcp-ajax-nonce' ),
		// 	]
		// );

		wp_register_script(
			'afcp-script-rest',
			AFCP_URI . 'assets/afcp-rest.js',
			['jquery'],
			filemtime(AFCP_DIR . 'assets/afcp-rest.js'),
			true
		);
		//'afcp/v1/add'
		wp_localize_script(
			'afcp-script-rest',
			'afcp_rest',
			[
				'root'   => esc_url_raw(rest_url()),
				'nonce' => wp_create_nonce('wp_rest'),
			]
		);

		wp_register_style(
			'afcp-select2-style',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css',
			[],
			null
		);

		wp_register_script(
			'afcp-select2-script',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js',
			['jquery', 'afcp-script'],
			null,
			true
		);
	}

	public function register_cpt_event()
	{

		$labels = [
			'name'          => 'Мероприятия',
			'singular_name' => 'Мероприятие',
			'add_new'       => 'Добавить мероприятие',
			'add_new_item'  => 'Добавить новое мероприятие',
			'edit_item'     => 'Редактировать мероприятие',
			'new_item'      => 'Новое мероприятие',
			'all_items'     => 'Мероприятия',
			'view_item'     => 'Просмотр мероприятия на сайте',
			'search_items'  => 'Искать мероприятие',
			'not_found'     => 'Ничего не найдено.',
			'menu_name'     => 'Мероприятия',
		];

		$args = [
			'labels'        => $labels,
			'public'        => true,
			'taxonomies'    => ['topics'],
			'show_in_rest'  => true,
			'has_archive'   => true,
			'query_var'     => true,
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 4,
			'supports'      => ['title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields'],
		];

		register_post_type('event', $args);
	}

	public function register_tax_topics()
	{

		$args = [
			'hierarchical' => true,
			'labels'       => [
				'name'          => 'Категории',
				'singular_name' => 'Категория',
				'menu_name'     => 'Категория мероприятий',
			],
			'public'       => true,
			'show_in_rest' => true,
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => [],
		];

		register_taxonomy('topics', ['event'], $args);
	}


	public function register_tax_hashtags()
	{

		$args = [
			'hierarchical' => false,
			'labels'       => [
				'name'          => 'Метки',
				'singular_name' => 'Метка',
				'menu_name'     => 'Метки мероприятий',
			],
			'public'       => true,
			'show_in_rest' => true,
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => [],
		];

		register_taxonomy('hashtags', ['event'], $args);
	}



	public static function instance()
	{

		if (is_null(self::$instanse)) {
			self::$instanse = new self();
		}
		return self::$instanse;
	}
}
