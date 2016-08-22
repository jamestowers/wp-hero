<?php

class Wp_Hero_Admin {

	private $plugin_name;

	private $version;

  private $post_types;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
    $this->post_types = array('post', 'project', 'album'); // Post types used in posts dropdown for hero link url

	}

	public function enqueue_styles() {

    wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-hero-admin.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

    wp_enqueue_media();
     
    /*wp_enqueue_script(
        $this->name,
        plugin_dir_url( __FILE__ ) . 'js/admin.js',
        array( 'jquery' ),
        $this->version,
        'all'
    );*/
    

		wp_enqueue_script( 
      $this->plugin_name, 
      plugin_dir_url( __FILE__ ) . 'js/wp-hero-admin.js', 
      array( 'jquery', 'wp-color-picker' ), 
      $this->version, 
      true
    );

    //wp_localize_script( $this->plugin_name, 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	/**
	 * Create Hero custom post type
	 */
	public function create_hero_post_type() {
	  register_post_type( 'hero',
	    array(
	      'labels' => array(
	        'name' => __( 'Features' ),
	        'singular_name' => __( 'Feature' ),
	        'add_new' => __( 'Add New Feature' ),
		      'add_new_item' => __( 'Add New Feature' ),
		      'edit_item' => 'Edit Feature',
		      'featured_image' => __( 'Feature Cover' ),
		      'use_featured_image' => __( 'Use as Feature cover' ),
		      'archives' => __( 'Feature archives' )
	      ),
	      'public' => true,
	      'menu_icon' => 'dashicons-star-filled',
        'supports' => array('title'),
	      'rewrite' => array( 
	      	'slug' => 'heros', 
	      	'with_front' => false 
	      )
	    )
	  );
	}

	public function post_meta_boxes_setup()
  {
    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', array( &$this, 'add_hero_meta_boxes') );
    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', array( &$this, 'save_hero_meta'), 10, 2 );
  }

  public function add_hero_meta_boxes($postType)
  {
    add_meta_box(
      $this->plugin_name . '_hero_editor_meta_box',      // Unique ID
      esc_html__( 'Editor', $this->plugin_name ),    // Title
      array( &$this, 'render_hero_editor_meta_box'),   // Callback function
      'hero',         // Admin page (or post type)
      'normal',       // Context
      'default'       // Priority
    );

    add_meta_box(
      $this->plugin_name . '_hero_media_meta_box',      // Unique ID
      esc_html__( 'Media', $this->plugin_name ),    // Title
      array( &$this, 'render_hero_media_meta_box'),   // Callback function
      'hero',         // Admin page (or post type)
      'normal',       // Context
      'default'       // Priority
    );

    add_meta_box(
      $this->plugin_name . '_hero_background_meta_box',      // Unique ID
      esc_html__( 'Background', $this->plugin_name ),    // Title
      array( &$this, 'render_hero_background_meta_box'),   // Callback function
      'hero',         // Admin page (or post type)
      'normal',       // Context
      'default'       // Priority
    );

    add_meta_box(
      $this->plugin_name . '_hero_meta_box',      // Unique ID
      esc_html__( 'Details', $this->plugin_name ),    // Title
      array( &$this, 'render_hero_info_meta_box'),   // Callback function
      'hero',         // Admin page (or post type)
      'normal',       // Context
      'default'       // Priority
    );

    add_meta_box(
      $this->plugin_name . '_hero_activate_meta_box',      // Unique ID
      esc_html__( 'Activate', $this->plugin_name ),    // Title
      array( &$this, 'render_hero_activate_meta_box'),   // Callback function
      'hero',         // Admin page (or post type)
      'side',         // Context
      'default'       // Priority
    );
  }

  /* Display the post meta box. */
  public function render_hero_editor_meta_box( $post, $box ) { 

      // Get the currently selected option
      //$post_meta = get_metadata('post', $post->ID);

      wp_nonce_field( basename( __FILE__ ),  'hero_editor_nonce' );

      $content = get_post_meta($post->ID, $this->plugin_name . '_copy', true);
      wp_editor( $content, $this->plugin_name . '_copy', $settings = array() );
      
  }

  public function render_hero_media_meta_box( $post, $box ) { 
      $current_media_type = get_post_meta($post->ID, $this->plugin_name . '_media-type', true);
      ?>
      <label>Media type</label><br />
      <select name="<?php echo $this->plugin_name;?>_media-type" id="<?php echo $this->plugin_name;?>_media-type">
        <option value="image" <?php echo $current_media_type == 'image' ? 'selected="selected"' : '';?>>Image</option>
        <option value="video" <?php echo $current_media_type == 'video' ? 'selected="selected"' : '';?>>Video</option>
      </select>
      <div class="spinner" style="float:none;width:auto;height:auto;padding:10px 0 10px 
50px;background-position:20px 0;"></div>
      <div id="wp_hero-media-fields"><?php $this->render_media_fields($post->ID, $current_media_type);?></div>

  <?php }

  public function get_media_fields($post_id = null, $media_type = null)
  {
    $this->render_media_fields($post_id, $media_type);
    wp_die();
  }

  public function render_media_fields($post_id, $media_type)
  {
    if(isset($_POST['mediaType'])){
      $post_id = $_POST['post_id'];
      $media_type = $_POST['mediaType'];
    }
    $post_meta = get_metadata('post', $post_id);

    log_it($post_id);

    switch ($media_type){

      case 'video':
        echo '<label>YouTube or Vimeo video URL</lable>';
        echo '<input type="url" class="large-text" name="' . $this->plugin_name . '_video-url" value="' . $post_meta[$this->plugin_name . '_video-url'][0] . '" />';

        echo '<label>Or embed code <em>(Will overwrite url above)</em></label><br />';
        echo '<textarea name="' . $this->plugin_name . '_video-embed-code" cols="80" rows="10" class="large-text">' . $post_meta[$this->plugin_name . '_video-embed-code'][0] . '</textarea>';
        break;

      case 'image' :
        $meta_key = $this->plugin_name . '_feature-image';
        $this->show_media_picker($post_id, $meta_key, 'Set feature image');
        break;
    }
  }



  /* Display the post meta box. */
  public function render_hero_background_meta_box( $object, $box ) { 

      $post_meta = get_metadata('post', $object->ID);
      //$hero_bg = get_post_meta($object->ID, $this->plugin_name . '_background', true);
      //log_it(isset($post_meta[$this->plugin_name . '_background-color']));

      wp_nonce_field( basename( __FILE__ ),  'hero_background_nonce' );?>

      <div class="hide-if-no-js">
        <p>
          <label>Background colour</label><br />
          <input type="text" value="<?php echo isset($post_meta[$this->plugin_name . '_background-color']) ? $post_meta[$this->plugin_name . '_background-color'][0] : '';?>" class="color-picker" name="<?php echo $this->plugin_name;?>_background-color" data-default-color="#ffffff" />
        </p>

        <label>Background image</label>
        <p class="description"><?php echo _e( "(Optional)", $this->plugin_name );?></p>

        <?php  $this->show_media_picker($object->ID, $this->plugin_name . '_background-image', 'Set background image');?>

        
        <p>
          <label>Repeat background?</label><br />
          <input type="radio" name="<?php echo $this->plugin_name;?>_background-repeat" value="repeat" /> Yes <br />
          <input type="radio" name="<?php echo $this->plugin_name;?>_background-repeat" value="no-repeat" /> No
        </p>

        <p>
          <label>Background alignment</label><br />
          <input type="radio" name="<?php echo $this->plugin_name;?>_background-align" value="left" /> Left <br />
          <input type="radio" name="<?php echo $this->plugin_name;?>_background-align" value="center" /> Center <br />
          <input type="radio" name="<?php echo $this->plugin_name;?>_background-align" value="right" /> Right
        </p>
      </div>
      

  <?php }

 	/* Display the post meta box. */
 	public function render_hero_info_meta_box( $object, $box ) { 

     // Get the currently selected option
     $link_url = get_post_meta($object->ID, $this->plugin_name . '_link-url', true);
     $post_id = get_post_meta($object->ID, $this->plugin_name . '_post-id', true);

     $args = array(
      'posts_per_page'   => -1,
      'post_type'        => $this->post_types,
      'orderby'          => 'post_type'
      );
     $posts = get_posts($args);

     // Add nonce field - use meta key name with '_nonce' appended
     wp_nonce_field( basename( __FILE__ ), $link_url . '_nonce' );

     echo '<p class="description">' .  _e( "Enter the URL to link the feature to (will override the <em>link to item</em> setting in dropdown below)", $this->plugin_name ) . '</p>';?>
     <p>
      <label>Link url</label><br />
      <input type="url" name="<?php echo $this->plugin_name;?>_link-url" value="<?php echo isset($link_url) ? $link_url : '' ;?>" class="large-text" placeholder="" />
    </p>

    <p>
     <label>Link to item</label><br />
     <select name="<?php echo $this->plugin_name;?>_post-id">
       <?php 
       $current_post_type = null;
       foreach($posts as $post){
        $selected = $post->ID == $post_id ? 'selected' : '';
        if($current_post_type != $post->post_type){
          echo '<optgroup label="' . ucfirst ($post->post_type) . '">';
        }
        echo '<option value="' . $post->ID . '" ' . $selected . '>' . $post->post_title . '</option>';
        $current_post_type = $post->post_type;
        if($current_post_type != $post->post_type){
          echo '</optgroup>';
        }
       }?>
     </select>
    <p>

 <?php }

 /* Display the post meta box. */
public function render_hero_activate_meta_box( $object, $box ) { 

     // Get the currently selected option
     $is_active = get_post_meta($object->ID, $this->plugin_name . '_is-active', true);

     // Add nonce field - use meta key name with '_nonce' appended
     //wp_nonce_field( basename( __FILE__ ), $link_url . '_nonce' );?>

     <p class="description"><?php  _e( "Make this the currently active feature (will remove the current one and replace it)", $this->plugin_name );?></p>

     <p>  
        <label>Make active</label>
        <input type="checkbox" name="<?php echo $this->plugin_name;?>_is-active" value="1" <?php checked( $is_active ); ?> />
     </p>


 <?php }



 private function show_media_picker($post_id, $meta_key, $label)
 {
   $current_media = get_post_meta($post_id, $meta_key, true); ?>

   <p class="hide-if-no-js  <?php echo $current_media != '' ? 'hidden' : '';?>">
     <a class="wp-hero-media-select button-secondary" title="Select media" href="javascript:;" data-meta-key="<?php echo $meta_key;?>"><?php echo $label;?></a>
   </p>

   <?php if($current_media != ''){ ?>

     <div class="image-container" data-meta-key="<?php echo $meta_key;?>">
       <img src="<?php echo $current_media;?>" alt="" title="Current feature image" />
     </div>

   <?php }else{ ?>

     <div class="image-container" data-meta-key="<?php echo $meta_key;?>" class="hidden"><img src="" alt="" title="" /></div>
   
   <?php }?>

   <p class="hide-if-no-js <?php echo $current_media != '' ? '' : 'hidden';?>">
     <a title="Remove Media" href="javascript:;" class="wp-hero-remove-media" data-meta-key="<?php echo $meta_key;?>">Remove</a>
   </p>
   
   <input type="text" class="image-src large-text" data-meta-key="<?php echo $meta_key;?>" name="<?php echo $meta_key;?>" value="<?php echo $current_media != '' ? $current_media : '';?>" />

 <?php }

  public function save_hero_meta( $post_id, $post )
  {
    $this->save_meta($post_id, $post, $this->plugin_name . '_media-type');
    $this->save_meta($post_id, $post, $this->plugin_name . '_feature-image');
    $this->save_meta($post_id, $post, $this->plugin_name . '_video-url');
    $this->save_meta($post_id, $post, $this->plugin_name . '_video-embed-code');
    $this->save_meta($post_id, $post, $this->plugin_name . '_copy');
    $this->save_meta($post_id, $post, $this->plugin_name . '_background-image');
    $this->save_meta($post_id, $post, $this->plugin_name . '_background-repeat');
    $this->save_meta($post_id, $post, $this->plugin_name . '_background-align');
    $this->save_meta($post_id, $post, $this->plugin_name . '_background-color'); 
    $this->save_meta($post_id, $post, $this->plugin_name . '_post-id');
    $this->save_meta($post_id, $post, $this->plugin_name . '_link-url'); 
    $this->save_meta($post_id, $post, $this->plugin_name . '_is-active'); 
  }



  public function save_meta($post_id, $post, $meta_key)
  {
    $this->verify_nonce($meta_key . '_nonce', $post_id);

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );
    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
      return $post_id;

    $new_meta_value = ( isset( $_POST[$meta_key] ) ? $_POST[$meta_key] : '' );

    $this->save_or_edit_meta($post_id, $meta_key, $new_meta_value);
  }



  public function verify_nonce($nonce_key, $post_id)
  {
    if ( !isset( $_POST[$nonce_key] ) || !wp_verify_nonce( $_POST[$nonce_key], basename( __FILE__ ) ) )
      return $post_id;
  }



  public function save_or_edit_meta($post_id, $meta_key, $new_meta_value)
  {
    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta( $post_id, $meta_key, true );

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value && '' == $meta_value )
      add_post_meta( $post_id, $meta_key, $new_meta_value, true );

    /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value && $new_meta_value != $meta_value )
      update_post_meta( $post_id, $meta_key, $new_meta_value );

    /* If there is no new meta value but an old value exists, delete it. */
    elseif ( '' == $new_meta_value && $meta_value )
      delete_post_meta( $post_id, $meta_key, $meta_value );
  }

}
