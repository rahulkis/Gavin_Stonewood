<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WC_Sales_Up
 * @subpackage WC_Sales_Up/public/partials
 */


   // Set $cat_in_cart to false
   $cat_in_cart = false;
 
   // Loop through all products in the Cart
   foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
 
      // If Cart has category "download", set $cat_in_cart to true
      if ( has_term( 'chopping-boards', 'product_cat', $cart_item['product_id'] ) ) {
         $cat_in_cart = true;
      }
      elseif ( has_term( 'cheese-boards', 'product_cat', $cart_item['product_id'] ) ) {
         $cat_in_cart = true;
      }
      elseif ( has_term( 'cutting-boards', 'product_cat', $cart_item['product_id'] ) ) {
         $cat_in_cart = true;
      }
   }
 
   // Do something if category "download" is in the Cart
   if ( $cat_in_cart==false) { ?>
 <style>
 	.wsp_preview{
 		display: none;
 	}
 </style>
   <?php }
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wsp_preview">
<style>
	.wsp_cho_title {
		background: <?php echo esc_attr( $wsp_cho_offer_title_bg_color ); ?>;
		color: <?php echo esc_attr( $wsp_cho_offer_title_color ); ?>;
	}

	.wsp_preview {
		background: <?php echo esc_attr( $wsp_cho_box_bg ); ?>;
	}

	#wsp_cho_content, #wsp_cho_content * {
		color: <?php echo esc_attr( $wsp_cho_offer_text_color ); ?>;
	}

	.wsp_cho_price>span, .wsp_cho_price del span {
		color: <?php echo esc_attr( $wsp_cho_price_color ); ?>;
	}

	.woocommerce-cart .wsp_preview {
		width : <?php echo esc_attr($wsp_cho_box_width); ?><?php echo esc_attr($wsp_cho_box_width_pre); ?>
	}
	#wsp_c_img_cover img{
		display: block;
	}
</style>

	<div class="wsp_cho_title">
		<input
		<?php
		if ( $product_data->is_type( 'variable' ) ) {
			//echo 'disabled=""';
			//echo 'class="disabled"'; 
		}
		?>
		type="checkbox" name="wsp_c_check" id="wsp_c_check" /><label for="wsp_c_check"><?php echo esc_attr( $wsp_cho_title ); ?></label>
	</div>
	<div class="wsp_cho_content">
		<div class="wsp_c_img_cover" id="wsp_c_img_cover"
		<?php
		if ( 'no' == $wsp_cho_display_image ) {
																echo "style='display:none'";
		}
		?>
															>
			<?php
			if ( 'yes' == $wsp_cho_display_link ) {
				?>
				<a target='_blank' href="<?php echo esc_url( get_the_permalink( $product_data->get_id() ) ); ?>">
				<?php
			}
			?>
				<img class="wsp_c_img_src" src="<?php echo esc_url( $wsp_cho_img_src ); ?>" alt="" />
				<?php
				if ( 'yes' == $wsp_cho_display_link ) {
					?>
				</a>
					<?php
				}
				?>
		</div>
		<div class="wsp_c_content_cover">
			<div id='wsp_cho_content'>
				<?php echo wp_kses( $wsp_cho_content, wsp_args_kses() ); ?>
				<?php
				if ( $product_data->is_type( 'variable' ) ) {
					$available_variations = WC_Sales_Up_Product::get_variations_from_product( $product_data );
					if ( is_array( $available_variations ) && ! empty( $available_variations ) ) {
						?>
						<select name="wsp_attribute_<?php echo esc_attr( $product_data->get_id() ); ?>" class="wsp_attribute_select wsp_attribute_<?php echo esc_attr( $product_data->get_id() ); ?>" id="my_price" onchange="get_price()">
							<option disabled='disabled' selected='selected' value=''>
								<?php echo esc_html( WC_Sales_Up_Product::get_attribute_placeholder( $product_data ) ); ?>
							</option>
							<?php
							foreach ( $available_variations as $available_variation ) {
								$option_attributes = array();
								foreach ( $available_variation['attributes'] as $attribute_key => $attribute_value ) {
									$option_attributes[] = $attribute_value;
								}
								$option_string = implode( ' - ', $option_attributes );
								$price = get_post_meta($available_variation['variation_id'], '_price', true);
								$discount_price=number_format((float)$price-($price * 0.1), 2, '.', '');
								?>
									<option
										value='<?php echo esc_attr( $available_variation['variation_id'] ); ?>' data-price="<?php echo $price; ?>"
										data-discount="<?php echo $discount_price; ?>"
										data-attributes="<?php echo esc_attr( wp_json_encode( $available_variation['attributes'] ) ); ?>">
									<?php echo esc_attr( $option_string ); ?>
									</option>
									<?php
							}
							?>
						</select>
						<?php
					}
					$product_id = '';
				} else {
					$product_id = $product_data->get_id();
				}
				?>
				<input type="hidden" id="wsp_cho_pro" name="wsp_cho_pro" value="<?php echo esc_attr( $product_id ); ?>" />
			</div>
			<div class="wsp_cho_price">
				<del><?php echo wp_kses( $original_price, wsp_args_kses() ); ?></del> <span class="wsp_d_price"><?php echo wp_kses( $off_price, wsp_args_kses() ); ?></span>
			</div>
		</div>
	</div>
</div>

<script>
// jQuery( document ).ready(function() {
//     alert( "ready!" );
// });
function get_price(){
  var price = jQuery('#my_price option:selected').attr("data-price");
  var discount = jQuery('#my_price option:selected').attr("data-discount");
  var price_html='<del><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>'+price+'</span></del> <span class="wsp_d_price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>'+discount+'</span></span>';
  jQuery('.wsp_cho_price').html(price_html);
}
</script>