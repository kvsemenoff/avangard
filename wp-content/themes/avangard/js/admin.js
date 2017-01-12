var file_frame;

jQuery(document).ready(function($){

	$('.mgop-wrapper-sortable').sortable();

	mgop_bind_delete_item = function(el){

		el.find('.mgop_remove_item').bind('click', function(event){

			event.preventDefault();

			$(this).parent().parent().fadeOut('fast', function(){
				$(this).remove();
			});

		});

	}

	mgop_bind_delete_item($('.mgop-wrapper-sortable'));

	$('.salon_product_image_add').on('click', function( event ){

		event.preventDefault();

		var the_for = $(this).attr('data-for');
		var the_title = $(this).attr('title');

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title	: 'Выберите фото для добавления в "'+ the_title +'"',
			button	: {
				text: 'Добавить в "'+ the_title +'"',
			},
			multiple: true  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {

			var selection = file_frame.state().get('selection');

			selection.map( function( attachment ) {

				attachment = attachment.toJSON();
				console.log(attachment);
				var the_list = $('<li class="mgop_thumnails" title="Перетаскивайте для сортировки"><div><span class="mgop-movable"></span><a href="#" class="mgop_remove_item" title="Click to delete this item"><span>delete</span></a><img src="'+ attachment.url +'"><input type="hidden" name="product_images[]" value="'+ attachment.id +'" /></div></li>').hide();

				mgop_bind_delete_item(the_list);

				$('#salon_product_images_' + the_for).append(the_list);

				the_list.fadeIn();
			});

		});

		// Finally, open the modal
		file_frame.open();
	});

});