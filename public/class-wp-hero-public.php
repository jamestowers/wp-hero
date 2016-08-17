<?php

class Wp_Hero_Public {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->add_shortcodes();

	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-hero-public.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-hero-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Enable shortcode
	 *
	 * Adds the [gfd_form] shortcode for displaying the submission form on a page
	 *
	 * @since    1.0.0
	 */
	public function add_shortcodes()
	{
		add_shortcode('wp_hero', array( &$this, 'show_hero'));
		//add_shortcode('wp_timeline_posts', array( &$this, 'get_timeline_posts'));
	}

	private function get_active_hero()
	{
		$args = array(
			'post_type' => 'hero',
			'meta_query' => array(
				array(
					'key' => $this->plugin_name . '_is-active',
					'value' => '1',
				)
			)
		 );

		$post = get_posts($args)[0];
		$meta = get_metadata('post', $post->ID);

		return $meta;
	}

	public function show_hero()
	{
		$prefix = $this->plugin_name . '_';
		$hero = $this->get_active_hero();?>

		<style>
			.wp-hero{
				background-color: <?php echo isset($hero[$prefix . 'background-color']) ? $hero[$prefix . 'background-color'][0] : 'transparent';?>;
				background-image: <?php echo isset($hero[$prefix . 'background-image']) ? $hero[$prefix . 'background-image'][0] : 'none';?>;
			}
		</style>

		<div class="wp-hero pad">
			
			<?php if(isset($hero[$prefix . 'feature-image'])){
				echo '<div class="wp-hero-thumbnail"><img src="' . $hero[$prefix . 'feature-image'][0] . '" /></div>';
			}?>

			<?php if(isset($hero[$prefix . 'copy'])){
				echo '<div class="wp-hero-copy">' . $hero[$prefix . 'copy'][0] . '</div>';
			}?>
				

		</div>

		<?php 
	}


}
