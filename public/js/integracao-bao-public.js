(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

/**
 * Função sendMinutaIdToBackEnd()
 * Envia o id da minuta junto ao post id para o backend
 * @param minutaId(int) = id number
 * @param postId(int) = id do post
 * @param ajaxUrl(str) = url wp admin ajax
 *
 * @since 1.0.2
 */
function sendMinutaIdToBackEnd(minutaId, postId, ajaxUrl)
{
	var minutaId = minutaId, 
		postId = postId,
		actionWp = 'save_minuta_id_on_product',
		ajaxUrl = ajaxUrl;

	jQuery.ajax({
		url: ajaxUrl,
		type: 'POST',
		data: {
			'action': actionWp,
			'minuta_id': minutaId,
			'post_id': postId
		},
		success: function(data)
		{
			console.log('Dados gravados com sucesso!');
		},
		error: function(err)
		{
			console.log(err);
		}
	});
}


		// 		}
				
		// 	}
		// });
	}

})( jQuery );
