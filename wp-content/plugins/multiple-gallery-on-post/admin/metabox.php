<?php/** * Class to register Multiple Gallery on Post metaboxes */class MGOP_Dynamic_Meta_Boxes {    private $boxes;		private $positions = array(		'after' => 'After post content',		'before' => 'Before post content',		'shortcode' => 'By Shortcode',	);    public function __construct( $args )    {        $this->boxes = $args;        add_action( 'plugins_loaded', array( $this, 'start_up' ) );    }    public function start_up()    {        add_action( 'add_meta_boxes', array( $this, 'add_mb' ) );		add_action( 'save_post', array( $this, 'mb_submit' ) );    }    public function add_mb()    {        foreach( $this->boxes as $box )            add_meta_box(                 $box['id'],                 $box['title'],                 array( $this, 'mb_callback' ),                 $box['post_type'],                 isset( $box['context'] ) ? $box['context'] : 'normal',                 isset( $box['priority'] ) ? $box['priority'] : 'default',                 $box['args']            );    }		public function mb_submit( $post_id ){        if(isset($_REQUEST['post_type'])){            $post_type = $_REQUEST['post_type'];        } else {            $post_type = '';        }		if ('page' == $post_type ) {			if ( ! current_user_can( 'edit_page', $post_id ) )				return;		} else {			if ( ! current_user_can( 'edit_post', $post_id ) )				return;		}		if ( ! isset( $_POST['mgop_noncename'] ) || ! wp_verify_nonce( $_POST['mgop_noncename'], plugin_basename( __FILE__ ) ) )			return;        $post_ID = filter_input(INPUT_POST, 'post_ID', FILTER_SANITIZE_SPECIAL_CHARS);        $data = filter_input(INPUT_POST, 'mgop_media', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);		add_post_meta($post_ID, 'mgop_media_value', $data, true) or		  update_post_meta($post_ID, 'mgop_media_value', $data);	}    public function mb_callback( $post, $box )    {			$value = get_post_meta( $post->ID, 'mgop_media_value', true );		$value = isset($value[ $box['id'] ]) ? $value[ $box['id'] ] : array();			wp_nonce_field( plugin_basename( __FILE__ ), 'mgop_noncename' );        ?>		<a href="#" class="mgop_add" data-for="<?php echo $box['id']; ?>" title="<?php echo $box['title']; ?>">Add Image</a>		<ul id="mgop_<?php echo $box['id']; ?>" class="mgop-wrapper-sortable">			<?php 			if(is_array($value) && count($value)){ 				foreach($value as $attc_id){					$url = wp_get_attachment_thumb_url( $attc_id );			?>				<li class="mgop_thumnails" title="Drag and drop to sort the item"><div><span class="mgop-movable"></span><a href="#" class="mgop_remove_item" title="Click to delete this item"><span>delete</span></a><img src="<?php echo $url ?>"><input type="hidden" name="mgop_media[<?php echo $box['id']; ?>][]" value="<?php echo $attc_id ?>" /></div></li>			<?php }			} ?>		</ul>		<div class="mgop-detail" style="border-top: 1px solid #ccc;">			<ul>				<li><label>Gallery Slug</label>: <code><?php echo $box['args']['slug']; ?></code></li>				<li><label>Output Location</label>: <?php echo $this->positions[ $box['args']['position'] ]; ?></li>				<li><label>Available Shortcodes</label>: <code><?php echo mgop_create_shortcode($box['args']['slug'], 'ul'); ?></code> or <code><?php echo mgop_create_shortcode($box['args']['slug'], 'ol'); ?></code> or <code><?php echo mgop_create_shortcode($box['args']['slug'], 'div'); ?></code></li>			</ul>		</div>		<div class="mgop-powerred">Powerred By: Multiple Gallery on Post By <a href="http://iwayanwirka.duststone.com">I Wayan Wirka</a>.</div>		<?php    }}// Add metabox$mgop_options = get_option('mgop_options');$mgop_mbs = array();if(is_array($mgop_options)){	foreach($mgop_options as $post_type => $vars){		if(is_array($vars) && isset($vars['active']) && $vars['active']){			foreach($vars['titles'] as $mgop_slug => $mgop_title){				$mgop_mbs[] = array(					'id' => 'mgop_mb_' . $mgop_slug,					'title' => $mgop_title['title'],					'post_type' => $post_type,					'args' => array(												'slug' => $mgop_slug,						'position' => $mgop_title['position'],					),				);			}		}	}}?>