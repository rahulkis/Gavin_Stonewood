(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
	$( document ).ready(
		function(){
			var active_tab = localStorage.getItem( "active_tab" );
			if (active_tab != null) {
				var a_active_tab = $( '#' + active_tab );
				if (a_active_tab.length) {
					var content_id = a_active_tab.attr( 'for' );
					$( '.wsp-tab' ).each(
						function(){
							$( this ).removeClass( 'active' );
						}
					);
					$( '.wsp-content' ).each(
						function(){
							$( this ).removeClass( 'active' );
						}
					);
					$( '#' + content_id ).addClass( 'active' );
					a_active_tab.addClass( 'active' );
				}
			}
			$( '.wsp_color' ).wpColorPicker();
			$( '.wsp_cho_products' ).each(
				function(){
					$( this ).select2(
						{
							ajax: {
								type: "post",
								url: ajaxurl,
								dataType: 'json',
								data: function (params) {
									var query = {
										action: 'get_all_products',
										search: params.term,
										type: 'public',
										wsp_nonce: ajax_object.wsp_nonce
									}
									return query;
								},
							}
						}
					);
				}
			);
			$( '#wsp_cho_categories' ).select2();
			$( '#wsp_cho_offer' ).select2(
				{
					ajax: {
						type: "post",
						url: ajaxurl,
						dataType: 'json',
						data: function (params) {
							var query = {
								action: 'get_all_products',
								search: params.term,
								type: 'public',
								wsp_nonce: ajax_object.wsp_nonce
							}
							return query;
						},
					}
				}
			);
			$( '.wsp-in' ).on(
				'click',
				'.wsp-tab',
				function(){
					localStorage.setItem( "active_tab", $( this ).attr( 'id' ) );
					$( '.wsp-tab' ).each(
						function(){
							$( this ).removeClass( 'active' );
						}
					);
					$( '.wsp-content' ).each(
						function(){
							$( this ).removeClass( 'active' );
						}
					);
					$( this ).addClass( 'active' );
					var this_id = '#' + $( this ).attr( 'for' );
					$( this_id ).addClass( 'active' );
				}
			);

			wsp_cho_display_on();
			$( '.wsp_field' ).on(
				'change',
				'#wsp_cho_display_on',
				function(){
					wsp_cho_display_on();
				}
			);

			function wsp_cho_display_on() {
				var wsp_cho_display_on = $( '#wsp_cho_display_on' ).val();
				$( '.specific_categories' ).each(
					function(){
						$( this ).hide();
					}
				);
				$( '.specific_products' ).each(
					function(){
						$( this ).hide();
					}
				);
				if (wsp_cho_display_on == 'specific_categories') {
					$( '.specific_categories' ).each(
						function(){
							$( this ).show();
						}
					);
				} else if (wsp_cho_display_on == 'specific_products') {
					$( '.specific_products' ).each(
						function(){
							$( this ).show();
						}
					);
				}
			}
			if ($( '#wsp_cho_offer' ).length) {
				wsp_change_customize();
			}
			$( '.wsp-cover' ).on(
				'change',
				'#wsp_cho_offer',
				function(){
					wsp_change_customize_product();
				}
			);
			$( '.wsp-cover' ).on(
				'change',
				'#wsp_cho_offer_amt',
				function(){
					wsp_change_customize();
				}
			);
			$( '.wsp-cover' ).on(
				'click',
				'#wsp_cho_offer_pre',
				function(){
					wsp_change_customize();
				}
			);
			function wsp_change_customize() {
				var wsp_cho_offer     = $( '#wsp_cho_offer' ).val();
				var wsp_cho_offer_amt = $( '#wsp_cho_offer_amt' ).val();
				var wsp_cho_offer_pre = $( '#wsp_cho_offer_pre' ).val();
				$.ajax(
					{
						type: "post",
						url: ajaxurl,
						dataType: 'json',
						data:{
							action:'offer_data',
							wsp_cho_offer: wsp_cho_offer,
							wsp_cho_offer_amt: wsp_cho_offer_amt,
							wsp_cho_offer_pre: wsp_cho_offer_pre,
							wsp_nonce: ajax_object.wsp_nonce
						},
						success:function(response){
							var original_price = response.original_price;
							var price1         = response.price;
							$( '#wsp_cho_price' ).children( 'del' ).html( original_price );
							$( '#wsp_cho_price' ).children( 'span' ).html( price1 );
						}
					}
				);
			}
			function wsp_change_customize_product() {
				var wsp_cho_offer     = $( '#wsp_cho_offer' ).val();
				var wsp_cho_offer_amt = $( '#wsp_cho_offer_amt' ).val();
				var wsp_cho_offer_pre = $( '#wsp_cho_offer_pre' ).val();
				$.ajax(
					{
						type: "post",
						url: ajaxurl,
						dataType: 'json',
						data:{
							action:'offer_data',
							wsp_cho_offer: wsp_cho_offer,
							wsp_cho_offer_amt: wsp_cho_offer_amt,
							wsp_cho_offer_pre: wsp_cho_offer_pre,
							wsp_nonce: ajax_object.wsp_nonce
						},
						success:function(response){
							var original_price = response.original_price;
							var price          = response.price;
							var image_id       = response.image_id;
							var image          = response.image;
							$( '#wsp_cho_price' ).find( 'del' ).html( original_price );
							$( '#wsp_cho_price' ).find( 'span' ).html( price );
							if (image_id != '') {
								$( '#wsp_cho_img' ).val( image_id );
							} else {
								$( '#wsp_cho_img' ).val( '' );
							}
							if (image != '' ) {
								$( '#wsp_c_img_cover_c' ).find( 'img' ).attr( 'src',image );
							} else {
								$( '#wsp_c_img_cover_c' ).find( 'img' ).attr( 'src',ajax_object.default_img );
							}
						}
					}
				);
			}
			wsp_change_preview();
			wsp_change_preview_hide_show();

			$( '#wsp_cho_box_bg' ).wpColorPicker(
				{
					change:function(event, ui) {
						var wsp_cho_box_bg = ui.color.toString();
						$( '#wsp_preview_c' ).css( 'background',wsp_cho_box_bg );
						$( '#wsp_cho_content' ).css( 'background',wsp_cho_box_bg );
						if ($( '#wsp_cho_offer_title_bg_color' ).val() == '') {
							$( '#wsp_cho_title' ).css( 'background',wsp_cho_box_bg );
						}
					},
					clear:function(event, ui) {
						var wsp_cho_box_bg = '';
						$( '#wsp_preview_c' ).css( 'background',wsp_cho_box_bg );
						$( '#wsp_cho_content' ).css( 'background',wsp_cho_box_bg );
						if ($( '#wsp_cho_offer_title_bg_color' ).val() == '') {
							$( '#wsp_cho_title' ).css( 'background',wsp_cho_box_bg );
						}
					}
				}
			);
			$( '#wsp_cho_offer_title_bg_color' ).wpColorPicker(
				{
					change:function(event, ui) {
						var wsp_cho_offer_title_bg_color = ui.color.toString();
						$( '.wsp_cho_title_c' ).css( 'background',wsp_cho_offer_title_bg_color );
						$( '#wsp_cho_title' ).css( 'background',wsp_cho_offer_title_bg_color );
					},
					clear:function(event, ui) {
						var wsp_cho_offer_title_bg_color = '';
						$( '.wsp_cho_title_c' ).css( 'background',wsp_cho_offer_title_bg_color );
						$( '#wsp_cho_title' ).css( 'background',wsp_cho_offer_title_bg_color );
					}
				}
			);
			$( '#wsp_cho_offer_title_color' ).wpColorPicker(
				{
					change:function(event, ui) {
						var wsp_cho_offer_title_color = ui.color.toString();
						$( '#wsp_cho_title' ).css( 'color',wsp_cho_offer_title_color );
					},
					clear:function(event, ui) {
						var wsp_cho_offer_title_color = '';
						$( '#wsp_cho_title' ).css( 'color',wsp_cho_offer_title_color );
					}
				}
			);
			$( '#wsp_cho_offer_text_color' ).wpColorPicker(
				{
					change:function(event, ui) {
						var wsp_cho_offer_text_color = ui.color.toString();
						$( '#wsp_cho_content' ).css( 'color',wsp_cho_offer_text_color );
					},
					clear:function(event, ui) {
						var wsp_cho_offer_text_color = '';
						$( '#wsp_cho_content' ).css( 'color',wsp_cho_offer_text_color );
					}
				}
			);
			$( '#wsp_cho_price_color' ).wpColorPicker(
				{
					change:function(event, ui) {
						var wsp_cho_price_color = ui.color.toString();
						$( '#wsp_cho_price' ).children( 'span' ).css( 'color',wsp_cho_price_color );
					},
					clear:function(event, ui) {
						var wsp_cho_price_color = '';
						$( '#wsp_cho_price' ).children( 'span' ).css( 'color',wsp_cho_price_color );
					}
				}
			);

			function wsp_change_preview() {
				var wsp_cho_box_bg               = $( '#wsp_cho_box_bg' ).val();
				var wsp_cho_offer_title_bg_color = $( '#wsp_cho_offer_title_bg_color' ).val();
				var wsp_cho_offer_title_color    = $( '#wsp_cho_offer_title_color' ).val();
				var wsp_cho_offer_text_color     = $( '#wsp_cho_offer_text_color' ).val();
				var wsp_cho_price_color          = $( '#wsp_cho_price_color' ).val();
				$( '#wsp_preview_c' ).css( 'background',wsp_cho_box_bg );
				$( '#wsp_cho_title' ).css( 'color',wsp_cho_offer_title_color );
				$( '.wsp_cho_title_c' ).css( 'background',wsp_cho_offer_title_bg_color );
				if (wsp_cho_offer_title_bg_color != '') {
					$( '#wsp_cho_title' ).css( 'background',wsp_cho_offer_title_bg_color );
				} else {
					$( '#wsp_cho_title' ).css( 'background',wsp_cho_box_bg );
				}
				$( '#wsp_cho_content' ).css( 'color',wsp_cho_offer_text_color );
				$( '#wsp_cho_content' ).css( 'background',wsp_cho_box_bg );
				$( '#wsp_cho_price' ).children( 'span' ).css( 'color',wsp_cho_price_color );
			}
			$( '.wsp-cover' ).on(
				'click',
				'#wsp_cho_display_image',
				function(){
					wsp_change_preview_hide_show();
				}
			);
			$( '.wsp-cover' ).on(
				'click',
				'#wsp_cho_display_price',
				function(){
					wsp_change_preview_hide_show();
				}
			);
			function wsp_change_preview_hide_show() {
				var wsp_cho_display_image = $( '#wsp_cho_display_image' ).val();
				var wsp_cho_display_price = $( '#wsp_cho_display_price' ).val();
				if (wsp_cho_display_image == 'yes') {
					$( '#wsp_c_img_cover_c' ).removeClass( 'wsp_hidden' );
				} else {
					$( '#wsp_c_img_cover_c' ).addClass( 'wsp_hidden' );
				}

				if (wsp_cho_display_price == 'yes') {
					$( '#wsp_cho_price' ).removeClass( 'wsp_hidden' );
				} else {
					$( '#wsp_cho_price' ).addClass( 'wsp_hidden' );
				}
			}

			$( '.wsp-cover' ).on(
				'click',
				'#wsp_c_img_edit_c',
				function(e){
					e.preventDefault();
					var button      = $( this ),
					custom_uploader = wp.media(
						{
							title: 'Insert image',
							library : {
								type : 'image'
							},
							button: {
								text: 'Use this image'
							},
							multiple: false
						}
					).on(
						'select',
						function() {
							var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
							$( '#wsp_c_img_src_c' ).attr( 'src',attachment.url );
							$( '#wsp_cho_img' ).val( attachment.id );
						}
					).open();

				}
			);

			$( '#the-list' ).sortable(
				{
					handle: ".wsp_sortable",
					update: function( event, ui ) {
						var $table  = $( '.wp-list-table' );
						var c_order = "";
						$( '#the-list tr' ).each(
							function(i){
								if (c_order == '') {
									c_order = $( this ).attr( 'id' );
								} else {
									c_order += "," + $( this ).attr( 'id' );
								}
							}
						);
						$.ajax(
							{
								type: 'POST',
								url: ajaxurl,
								dataType: 'json',
								data: {
									action: 'sort_cho_priority',
									list_data: c_order,
									posts: ajax_object.cho_posts,
									wsp_nonce: ajax_object.wsp_nonce
								},
								success: function( data ) {
								}
							}
						);
					},
				}
			);

			$( '.wsp_field' ).on(
				'keyup',
				'#wsp_cho_title_t',
				function(){
					$( '#wsp_cho_title' ).html( $( this ).val() );
				}
			);

			$( '.wsp_field' ).on(
				'input',
				'#wsp_cho_content_t',
				function(){
					$( '#wsp_cho_content' ).html( $( this ).val() );
				}
			);

		}
	);
})( jQuery );
