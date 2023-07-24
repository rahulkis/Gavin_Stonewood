(function( $ ) {
	'use strict';

	$(
		function() {

			$( '.checkout' ).on(
				'change',
				'#wsp_c_check',
				function(){
					$.ajax(
						{
							type: "post",
							url: custom_js_vars.ajaxurl,
							data:{
								action:'add_offer_to_cart',
								wsp_cho_pro_id : $( '#wsp_cho_pro' ).val(),
								wsp_nonce : custom_js_vars.wsp_nonce,
								wsp_from_cart : 'checkout'
							},
							success:function(response){
								$( 'body' ).trigger( 'update_checkout' );
							}
						}
					);
				}
			);

			$( '.cart-collaterals' ).on(
				'change',
				'#wsp_c_check',
				function(){
					$.ajax(
						{
							type: "post",
							url: custom_js_vars.ajaxurl,
							data:{
								action:'add_offer_to_cart',
								wsp_cho_pro_id : $( '#wsp_cho_pro' ).val(),
								wsp_nonce : custom_js_vars.wsp_nonce,
								wsp_from_cart : 'cart'
							},
							success:function(response){
								location.reload();
							}
						}
					);
				}
			);
			$(document).on('change','.variation_id', function(){
				var wsp_p_variation_id = $(this).val();
				$('#fbq_variable_product').val(wsp_p_variation_id);
				wsp_disabled_add_to_cart();
			});

			$( document ).on(
				'click',
				'.wsp_fbt_add_to_cart_ajax',
				function(e){
					e.preventDefault();
					if($('#fbq_variable_product').length) {
						var fbq_variable_product =  $('#fbq_variable_product').val();
					}
					else {
						var fbq_variable_product = 0;
					}
					var pro_ids = [];
					$( '.fbq_products' ).each(
						function(){
							if ($( this ).closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).length && $( this ).closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).val() != '') {
								pro_ids.push( $( this ).closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).val() );
							} else {
								pro_ids.push( $( this ).val() );
							}
						}
					);
					$.ajax(
						{
							type: "post",
							url: custom_js_vars.ajaxurl,
							data:{
								action:'submit_fbt',
								main_product_id : $( '#fbq_main_product' ).val(),
								fbq_variable_product: fbq_variable_product,
								products : pro_ids,
								wsp_nonce : custom_js_vars.wsp_nonce
							},
							success:function(response){
								window.location.href = custom_js_vars.wc_get_cart_url;
							}
						}
					);
				}
			);

			if ($( '#fbq_main_product' ).length) {
				wsp_disabled_add_to_cart();
			}

			$( '.wsp_fbt_product_list' ).on(
				'change',
				'select',
				function(){
					wsp_disabled_add_to_cart();
					wsp_change_single_price( $( this ) );
					wsp_change_offer_price();
				}
			);
			function wsp_disabled_add_to_cart(){
				var wps_add_btn = 'on';
				$( '.wsp_attribute_select' ).each(
					function(){
						if ($( this ).val() == '' || $( this ).val() == null) {
							wps_add_btn = 'off';
						} else {
							$( this ).closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).val( $( this ).val() );
						}
					}
				);
				if($('#fbq_variable_product').length) {
					if($('#fbq_variable_product').val() > 0) {
						wps_add_btn = 'on';
					}
					else {
						wps_add_btn = 'off';
					}
				}
				if (wps_add_btn == 'on') {
					$( "#wsp_fbt_add_to_cart" ).removeAttr( 'disabled' );
					$( "#wsp_fbt_add_to_cart" ).removeClass( 'disabled' );
				} else {
					$( "#wsp_fbt_add_to_cart" ).prop( 'disabled', true );
					$( "#wsp_fbt_add_to_cart" ).addClass( 'disabled' );
				}

			}

			function wsp_change_single_price(cur_obj) {
				$.ajax(
					{
						type: "post",
						url: custom_js_vars.ajaxurl,
						data:{
							action:'change_single_product_price',
							variation_id : cur_obj.closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).val(),
							wsp_nonce : custom_js_vars.wsp_nonce
						},
						success:function(response){
							var obj                  = JSON.parse( response );
							var total_original_price = obj.total_original_price;
							cur_obj.closest( '.wsp_fbt_product_list_li' ).find( '.wsp_price_inr' ).html( total_original_price );
						}
					}
				);
			}

			function wsp_change_offer_price() {
				var pro_ids = [];
				$( '.fbq_products' ).each(
					function(){
						if ($( this ).closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).length && $( this ).closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).val() != '') {
							pro_ids.push( $( this ).closest( '.wsp_fbt_product_list_li' ).find( '.wsp_variation_id' ).val() );
						} else {
							pro_ids.push( $( this ).val() );
						}
					}
				);
				$.ajax(
					{
						type: "post",
						url: custom_js_vars.ajaxurl,
						data:{
							action:'change_offer_price',
							main_product_id : $( '#fbq_main_product' ).val(),
							fbq_products : pro_ids,
							wsp_nonce : custom_js_vars.wsp_nonce
						},
						success:function(response){
							var obj                  = JSON.parse( response );
							var total_original_price = obj.total_original_price;
							var discount             = obj.discount;
							var discounted_price     = obj.discounted_price;
							$( '.wsp_fbt_total del' ).html( total_original_price );
							$( '.wsp_d_price' ).html( discounted_price );
							$( '.wsp_fbt_total_save span' ).html( discount );
						}
					}
				);
			}

			$( '#wsp_cho_content' ).on(
				'change',
				'select',
				function(){
					var page = 'cart';
					wsp_disabled_checkbox(page);
				}
			);
			$( '#order_review' ).on(
				'change',
				'select',
				function(){
					var page = 'checkout';
					wsp_disabled_checkbox( page );				}
			);
			function wsp_disabled_checkbox(page){
				var wps_add_btn = 'on';
				$( '.wsp_attribute_select' ).each(
					function(){
						if ($( this ).val() == '' || $( this ).val() == null) {

						} else {
							$( '#wsp_cho_pro' ).val( $( this ).val() );
						}
					}
				);
				wsp_change_cho_offer_price(page);
			}

			function wsp_change_cho_offer_price(page) {
				$.ajax(
					{
						type: "post",
						url: custom_js_vars.ajaxurl,
						data:{
							action:'change_cho_offer_price',
							wsp_cho_pro_id : $( '#wsp_cho_pro' ).val(),
							wsp_nonce : custom_js_vars.wsp_nonce,
							wsp_from_cart: page
						},
						success:function(response){
							if (response != '') {
								var obj              = JSON.parse( response );
								var original_price   = obj.original_price;
								var discounted_price = obj.discounted_price;
								$( '.wsp_cho_price del' ).html( original_price );
								$( '.wsp_d_price' ).html( discounted_price );

								$( "#wsp_c_check" ).removeAttr( 'disabled' );
								$( "#wsp_c_check" ).removeClass( 'disabled' );
							} else {
								$( "#wsp_c_check" ).prop( 'disabled', true );
								$( "#wsp_c_check" ).addClass( 'disabled' );
							}

						}
					}
				);
			}
		}
	);

})( jQuery );
