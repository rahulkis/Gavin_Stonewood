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
<div class='wrap wsp-cover'>
	<h2><?php esc_html_e( 'On Product page offer settings', 'wc-sales-up' ); ?></h2>
	<div class="wsp-inner">
		<form method="post">
			<input type="hidden" name="wsp_nonce" value="<?php echo wp_create_nonce( 'wsp_nonce' ); ?>" />
			<input type="submit" name="wsp_on_product_data" class="button button-primary button-large button-on-product" value="<?php esc_html_e( 'Save Changes', 'wc-sales-up' ); ?>" />
			<div class="wsp-in">
				<label class="wsp-tab active" for="tab1"><?php esc_html_e( 'General', 'wc-sales-up' ); ?></label>
				<label class="wsp-tab" for="tab2"><?php esc_html_e( 'Design', 'wc-sales-up' ); ?></label>			
				<ul>
					<li id="tab1" class="typography active wsp-content">
						<table class="wsp-table">
							<tbody>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Select Layout', 'wc-sales-up' ); ?></label>
											<select name="wsp_pro[design]">
												<option value="layout-1" <?php selected( $wsp_pro_design, 'layout-1' ); ?> ><?php esc_html_e( 'Layout 1', 'wc-sales-up' ); ?></option>
												<option value="layout-2" <?php selected( $wsp_pro_design, 'layout-2' ); ?> ><?php esc_html_e( 'Layout 2', 'wc-sales-up' ); ?></option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Offer title', 'wc-sales-up' ); ?></label>
											<input type="text" value="<?php echo esc_attr( $wsp_pro_offer_title ); ?>" name="wsp_pro[offer_title]" />
											<note><?php esc_html_e( 'Note : Leave blank to hide title', 'wc-sales-up' ); ?></note>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel" for="wsp_pro_link"><?php esc_html_e( 'Enable link on product titles?', 'wc-sales-up' ); ?></label>
											<input type="checkbox" id="wsp_pro_link" value="1" <?php checked( '1', $wsp_pro_link ); ?> name="wsp_pro[link]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel" for="wsp_pro_cart"><?php esc_html_e( 'Hide product if already in cart?', 'wc-sales-up' ); ?></label>
											<input type="checkbox" id="wsp_pro_cart" value="1" <?php checked( '1', $wsp_pro_cart ); ?> name="wsp_pro[cart]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel" for="wsp_pro_enable_ajax"><?php esc_html_e( 'Enable AJAX for "Add Selected to Cart" button?', 'wc-sales-up' ); ?></label>
											<input type="checkbox" id="wsp_pro_enable_ajax" value="1" <?php checked( '1', $wsp_pro_enable_ajax ); ?> name="wsp_pro[enable_ajax]" />
										</div>
									</td>
								</tr>								
							</tbody>
						</table>
					</li>
					<li id="tab2" class="typography wsp-content">
						<table class="wsp-table">
						<tbody>
							<tr>
								<td>
									<div class='wsp_field'>
										<label class="sublabel"><?php esc_html_e( 'Offer title color', 'wc-sales-up' ); ?></label>
										<input type="text" class="wsp_color" value="<?php echo esc_attr( $wsp_pro_offer_title_color ); ?>" name="wsp_pro[offer_title_color]" />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='wsp_field'>
										<label class="sublabel"><?php esc_html_e( 'Purchase Box background', 'wc-sales-up' ); ?></label>
										<input type="text" class="wsp_color" value="<?php echo esc_attr( $wsp_pro_box_bg ); ?>" name="wsp_pro[box_bg]" />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='wsp_field'>
										<label class="sublabel"><?php esc_html_e( 'Price color', 'wc-sales-up' ); ?></label>
										<input type="text" class="wsp_color" value="<?php echo esc_attr( $wsp_pro_price_color ); ?>" name="wsp_pro[price_color]" />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='wsp_field'>
										<label class="sublabel"><?php esc_html_e( 'Purchase discount text color', 'wc-sales-up' ); ?></label>
										<input type="text" class="wsp_color" value="<?php echo esc_attr( $wsp_pro_discount_color ); ?>" name="wsp_pro[discount_color]" />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='wsp_field'>
										<label class="sublabel"><?php esc_html_e( 'Purchase button color', 'wc-sales-up' ); ?></label>
										<input type="text" class="wsp_color" value="<?php echo esc_attr( $wsp_pro_button_color ); ?>" name="wsp_pro[button_color]" />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='wsp_field'>
										<label class="sublabel"><?php esc_html_e( 'Purchase button background', 'wc-sales-up' ); ?></label>
										<input type="text" class="wsp_color" value="<?php echo esc_attr( $wsp_pro_button_bg_color ); ?>" name="wsp_pro[button_bg_color]" />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='wsp_field'>
										<label class="sublabel"><?php esc_html_e( 'Arrow color', 'wc-sales-up' ); ?></label>
										<input type="text" class="wsp_color" value="<?php echo esc_attr( $wsp_pro_arrow_color ); ?>" name="wsp_pro[arrow_color]" />
									</div>
								</td>
							</tr>
						</tbody>
					</li>
				</ul>
			</div>
		</form>
	</div>
</div>
