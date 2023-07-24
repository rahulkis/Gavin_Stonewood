<?php
/**
 * Provide a admin area view for on product tab
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WC_Sales_Up
 * @subpackage WC_Sales_Up/admin/partials
 */

?>
<div id="wsp_together_product_settings_cblk" class="panel woocommerce_options_panel">
<input type="hidden" name="wsp_nonce" value="<?php echo wp_create_nonce( 'wsp_nonce' ); ?>" />
	<div class="options_group hide_if_grouped hide_if_external">
		<div class="options_group">
			<h3 class='wsp_h3'><?php esc_html_e( 'Frequently Bought Twogether', 'wc-sales-up' ); ?></h3>
			<p>
				<?php esc_html_e( 'Select products which you want to display in frequently bought twogther offer', 'wc-sales-up' ); ?>
			</p>
			<p class="form-field">
				<label><?php esc_html_e( 'Products', 'wc-sales-up' ); ?></label>
				<select class="wsp_cho_products short" multiple='multiple' name="wsp_tp[products][]">
					<?php
					if ( is_array( $wsp_tp_products_array ) && ! empty( $wsp_tp_products_array ) ) {
						foreach ( $wsp_tp_products_array as $key => $val ) {
							?>
							<option selected='selected' value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</p>
			<p class="form-field">
				<label><?php esc_html_e( 'Discount', 'wc-sales-up' ); ?></label>
				<input type='number' value="<?php echo esc_attr( $wsp_tp_discount ); ?>" name="wsp_tp[discount]" />
				<select name="wsp_tp[offer_pre]">
					<option <?php selected( $wsp_tp_offer_pre, 'percent' ); ?> value="percent">%</option>
					<option <?php selected( $wsp_tp_offer_pre, 'fix' ); ?> value="fix">$</option>
				</select>
			</p>
		</div>
	</div>
</div>
