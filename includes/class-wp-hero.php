<?php

class Wp_Hero {

	protected $loader;

	protected $plugin_name;

	protected $version;

	public function __construct() {

		$this->plugin_name = 'wp-hero';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-hero-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-hero-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-hero-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-hero-public.php';

		$this->loader = new Wp_Hero_Loader();

	}


	private function set_locale() {

		$plugin_i18n = new Wp_Hero_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


	private function define_admin_hooks() {

		$plugin_admin = new Wp_Hero_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_admin, 'create_hero_post_type' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'save_post', $plugin_admin, 'save_hero_meta' );
		$this->loader->add_action( 'load-post.php', $plugin_admin, 'post_meta_boxes_setup' );
		$this->loader->add_action( 'load-post-new.php', $plugin_admin, 'post_meta_boxes_setup' );

		$this->loader->add_action( 'wp_ajax_get_media_fields', $plugin_admin, 'get_media_fields' );

	}


	private function define_public_hooks() {

		$plugin_public = new Wp_Hero_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}


	public function run() {
		$this->loader->run();
	}

	
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	public function get_loader() {
		return $this->loader;
	}


	public function get_version() {
		return $this->version;
	}

}
