<?php
/**
 * Handles salon taxonomy in admin
 *
 * @class    Salon_Taxonomy
 * @version  1.0.0
 * @package  salon
 * @category Class
 * @author   cancer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Salon_Taxonomy class.
 */
class Salon_Taxonomy {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Category/term ordering
		add_action( 'create_term', array( $this, 'create_term' ), 5, 3 );
		add_action( 'delete_term', array( $this, 'delete_term' ), 5 );

		// Add form
		add_action( 'salon_category_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'salon_category_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		// Add columns
		add_filter( 'manage_edit-salon_category_columns', array( $this, 'salon_category_columns' ) );
		add_filter( 'manage_salon_category_custom_column', array( $this, 'salon_category_column' ), 10, 3 );

		// Taxonomy page descriptions
		add_action( 'salon_category_pre_add_form', array( $this, 'salon_category_description' ) );

		// Maintain hierarchy of terms
		add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );

	}

	/**
	 * Order term when created (put in position 0).
	 *
	 * @param mixed $term_id
	 * @param mixed $tt_id
	 * @param mixed $taxonomy
	 */
	public function create_term( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'salon_category' != $taxonomy  ) {
			return;
		}

		$meta_name = 'thumbnail_id';

        add_salon_term_meta( $term_id, $meta_name, 0 );
	}

	/**
	 * When a term is deleted, delete its meta.
	 *
	 * @param mixed $term_id
	 */
	public function delete_term( $term_id ) {
		global $wpdb;

		$term_id = absint( $term_id );

		if ( $term_id ) {
			$wpdb->delete( $wpdb->salon_termmeta, array( 'salon_term_id' => $term_id ), array( '%d' ) );
		}
	}

	/**
	 * Category thumbnail fields.
	 */
	public function add_category_fields() {
		?>
		<div class="form-field">
			<label><?php _e( 'Thumbnail', 'avangard' ); ?></label>
			<div id="salon_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<input type="hidden" id="salon_category_thumbnail_id" name="salon_category_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'avangard' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'avangard' ); ?></button>
			</div>
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery( '#salon_category_thumbnail_id' ).val() ) {
					jQuery( '.remove_image_button' ).hide();
				}

				// Uploading files
				var file_frame;

				jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( "Choose an image", "avangard" ); ?>',
						button: {
							text: '<?php _e( "Use image", "avangard" ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						var attachment = file_frame.state().get( 'selection' ).first().toJSON();

						jQuery( '#salon_category_thumbnail_id' ).val( attachment.id );
						jQuery( '#salon_category_thumbnail img' ).attr( 'src', attachment.sizes.thumbnail.url );
						jQuery( '.remove_image_button' ).show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery( document ).on( 'click', '.remove_image_button', function() {
					jQuery( '#salon_category_thumbnail img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
					jQuery( '#salon_category_thumbnail_id' ).val( '' );
					jQuery( '.remove_image_button' ).hide();
					return false;
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public function edit_category_fields( $term ) {

		$thumbnail_id = absint( get_salon_term_meta( $term->term_id, 'thumbnail_id', true ) );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = wc_placeholder_img_src();
		}
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'avangard' ); ?></label></th>
			<td>
				<div id="salon_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="salon_category_thumbnail_id" name="salon_category_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'avangard' ); ?></button>
					<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'avangard' ); ?></button>
				</div>
				<script type="text/javascript">

					// Only show the "remove image" button when needed
					if ( '0' === jQuery( '#salon_category_thumbnail_id' ).val() ) {
						jQuery( '.remove_image_button' ).hide();
					}

					// Uploading files
					var file_frame;

					jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php _e( "Choose an image", "avangard" ); ?>',
							button: {
								text: '<?php _e( "Use image", "avangard" ); ?>'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							var attachment = file_frame.state().get( 'selection' ).first().toJSON();

							jQuery( '#salon_category_thumbnail_id' ).val( attachment.id );
							jQuery( '#salon_category_thumbnail img' ).attr( 'src', attachment.sizes.thumbnail.url );
							jQuery( '.remove_image_button' ).show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery( document ).on( 'click', '.remove_image_button', function() {
						jQuery( '#salon_category_thumbnail img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#salon_category_thumbnail_id' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * save_category_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['salon_category_thumbnail_id'] ) && 'salon_category' === $taxonomy ) {
			update_salon_term_meta( $term_id, 'thumbnail_id', absint( $_POST['salon_category_thumbnail_id'] ) );
		}
	}

	/**
	 * Description for salon_category page to aid users.
	 */
	public function salon_category_description() {
		echo wpautop( __( 'Salon page.', 'avangard' ) );
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @param mixed $columns
	 * @return array
	 */
	public function salon_category_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['thumb'] = __( 'Image', 'avangard' );

		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function salon_category_column( $columns, $column, $id ) {

		if ( 'thumb' == $column ) {

			$thumbnail_id = get_salon_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = wc_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'avangard' ) . '" class="wp-post-image" height="48" width="48" />';

		}

		return $columns;
	}

	/**
	 * Maintain term hierarchy when editing a product.
	 *
	 * @param  array $args
	 * @return array
	 */
	public function disable_checked_ontop( $args ) {

		if ( 'salon_category' == $args['taxonomy'] ) {
			$args['checked_ontop'] = false;
		}

		return $args;
	}
}

new Salon_Taxonomy();
