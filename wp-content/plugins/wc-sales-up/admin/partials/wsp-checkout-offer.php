<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WC_Sales_Up
 * @subpackage WC_Sales_Up/admin/partials
 */

?>

<div class='wrap wsp-cover'>
	<input type="hidden" name="wsp_nonce" value="<?php echo wp_create_nonce( 'wsp_nonce' ); ?>" />
	<div class="wsp-inner">
			<div class="wsp-in">
				<label id="tab_1" class="wsp-tab active" for="tab1"><?php esc_html_e( 'Product', 'wc-sales-up' ); ?></label>
				<label id="tab_2" class="wsp-tab" for="tab2"><?php esc_html_e( 'Offer', 'wc-sales-up' ); ?></label>
				<label id="tab_3" class="wsp-tab" for="tab3"><?php esc_html_e( 'Bump Design', 'wc-sales-up' ); ?></label>
				<label id="tab_4" class="wsp-tab wsp-tab-4" for="tab4"><?php esc_html_e( 'PopUp Design', 'sales-up-pro' ); ?></label>
				<ul>
					<li id="tab1" class="wsp-content active typography">
						<table class="wsp-table">
							<tbody>
								<tr>
									<th>
										<div class="wsp_field">
											<label><?php esc_html_e( 'Select Category or Products', 'wc-sales-up' ); ?></label>
											<note><?php esc_html_e( 'Enable offer when following products in cart', 'wc-sales-up' ); ?></note>
										</div>
									</th>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Display offer on page:', 'wc-sales-up' ); ?></label>
											<select id="wsp_cho_display_page" name="wsp_cho[display_page][]" multiple>
												<option
												<?php
												if ( in_array( 'cart', $wsp_cho_display_page ) ) {
													echo 'selected'; }
												?>
												 value="cart"><?php esc_html_e( 'Cart', 'sales-up-pro' ); ?></option>
												<option
												<?php
												if ( in_array( 'checkout_bump', $wsp_cho_display_page ) ) {
													echo 'selected'; }
												?>
												 value="checkout_bump"><?php esc_html_e( 'Checkout Bump', 'sales-up-pro' ); ?></option>

											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Display offer for:', 'wc-sales-up' ); ?></label>
											<select id="wsp_cho_display_on" name="wsp_cho[display_on]">
												<option value="">All</option>
												<option <?php selected( $wsp_cho_display_on, 'specific_categories' ); ?> value="specific_categories"><?php esc_html_e( 'Specific Categories', 'wc-sales-up' ); ?></option>
												<option <?php selected( $wsp_cho_display_on, 'specific_products' ); ?> value="specific_products"><?php esc_html_e( 'Specific Products', 'wc-sales-up' ); ?></option>
											</select>
										</div>
									</td>
								</tr>
								<tr class="specific_categories">
									<th>
										<hr/>
									</th>
								</tr>
								<tr class="specific_categories">
									<td>
										<div class='wsp_field'>
											<?php esc_html_e( 'Display offer when', 'wc-sales-up' ); ?>
											<select name="wsp_cho[categories_all]">
												<option <?php selected( $wsp_cho_categories_all, 'any' ); ?> value="any"><?php esc_html_e( 'Any', 'wc-sales-up' ); ?></option>
												<option <?php selected( $wsp_cho_categories_all, 'all' ); ?> value="all"><?php esc_html_e( 'All', 'wc-sales-up' ); ?></option>
											</select>
											<?php esc_html_e( 'of following product categories are in cart', 'wc-sales-up' ); ?>
										</div>
									</td>
								</tr>
								<tr class="specific_categories">
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Categories:', 'wc-sales-up' ); ?></label>
											<select multiple='multiple' name="wsp_cho[categories][]" id="wsp_cho_categories">
												<?php
												$wsp_orderby = 'name';
												$wsp_order   = 'asc';
												$hide_empty  = false;
												$cat_args    = array(
													'orderby'    => $wsp_orderby,
													'order'      => $wsp_order,
													'hide_empty' => $hide_empty,
												);

												$product_categories = get_terms( 'product_cat', $cat_args );
												if ( $product_categories ) {
													foreach ( $product_categories as $key => $category ) {
														?>
														<option
														<?php
														if ( in_array( $category->term_id, $wsp_cho_categories ) ) {
															echo 'selected';
														};
														?>
														value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option>
														<?php
													}
												}
												?>
											</select>
										</div>
									</td>
								</tr>
								<tr class="specific_products">
									<th>
										<hr/>
									</th>
								</tr>
								<tr class='specific_products'>
									<td>
										<div class='wsp_field'>
											<?php esc_html_e( 'Display offer when', 'wc-sales-up' ); ?>
											<select name="wsp_cho[products_all]">
												<option <?php selected( $wsp_cho_products_all, 'any' ); ?> value="any"><?php esc_html_e( 'Any', 'wc-sales-up' ); ?></option>
												<option <?php selected( $wsp_cho_products_all, 'all' ); ?> value="all"><?php esc_html_e( 'All', 'wc-sales-up' ); ?></option>
											</select>
											<?php esc_html_e( 'of following products are in cart', 'wc-sales-up' ); ?>
										</div>
									</td>
								</tr>
								<tr class='specific_products'>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Products:', 'wc-sales-up' ); ?></label>
											<select multiple='multiple' name="wsp_cho[products][]" class="wsp_cho_products">
												<?php
												if ( is_array( $wsp_cho_products ) && ! empty( $wsp_cho_products ) ) {
													$wsp_cho_products_string = implode( ',', $wsp_cho_products );
													$all_products            = wsp_get_products_from_id( $wsp_cho_products );
													foreach ( $all_products as $key => $val ) {
														?>
														<option selected='selected' value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val ); ?></option>
														<?php
													}
												}
												?>
											</select>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
					<li id="tab2" class="wsp-content typography">
						<table class="wsp-table">
							<tbody>
								<tr>
									<th>
										<div class='wsp_field'>
											<label><?php esc_html_e( 'Create Offer', 'wc-sales-up' ); ?></label>
										</div>
									</th>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Products:', 'wc-sales-up' ); ?></label>

											<select name="wsp_cho[offer]" id="wsp_cho_offer">
												<?php
												if ( '' != $wsp_cho_offer ) {
													$t_product = $wpdb->get_var( $wpdb->prepare( 'select post_title from ' . $wpdb->prefix . 'posts where id = %d', $wsp_cho_offer ) );
													?>
													<option selected='selected' value="<?php echo esc_attr( $wsp_cho_offer ); ?>"><?php echo esc_html( $t_product ); ?></option>
													<?php
												}
												?>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Discount:', 'wc-sales-up' ); ?></label>
											<input id="wsp_cho_offer_amt" name="wsp_cho[offer_amt]" type="number" min='0' value="<?php echo esc_attr( $wsp_cho_offer_amt ); ?>" />
											<select id="wsp_cho_offer_pre" name="wsp_cho[offer_pre]" >
												<option <?php selected( $wsp_cho_offer_pre, 'percent' ); ?> value="percent">%</option>
												<option <?php selected( $wsp_cho_offer_pre, 'fix' ); ?> value="fix">$</option>
											</select>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
					<li id="tab3" class="wsp-content typography">
						<table class="wsp-table wsp_preview_tab wsp_50 first">
							<tbody>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Box background', 'wc-sales-up' ); ?></label>
											<input type="text" id="wsp_cho_box_bg" class="wsp_color" value="<?php echo esc_attr( $wsp_cho_box_bg ); ?>" name="wsp_cho[box_bg]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Title Bg color', 'wc-sales-up' ); ?></label>
											<input type="text" id="wsp_cho_offer_title_bg_color" class="wsp_color" value="<?php echo esc_attr( $wsp_cho_offer_title_bg_color ); ?>" name="wsp_cho[offer_title_bg_color]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Offer title color', 'wc-sales-up' ); ?></label>
											<input type="text" id="wsp_cho_offer_title_color" class="wsp_color" value="<?php echo esc_attr( $wsp_cho_offer_title_color ); ?>" name="wsp_cho[offer_title_color]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Offer text color', 'wc-sales-up' ); ?></label>
											<input type="text" id="wsp_cho_offer_text_color" class="wsp_color" value="<?php echo esc_attr( $wsp_cho_offer_text_color ); ?>" name="wsp_cho[offer_text_color]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Price color', 'wc-sales-up' ); ?></label>
											<input type="text" id="wsp_cho_price_color" class="wsp_color" value="<?php echo esc_attr( $wsp_cho_price_color ); ?>" name="wsp_cho[price_color]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Display Image?', 'wc-sales-up' ); ?></label>
											<select id="wsp_cho_display_image" name="wsp_cho[display_image]" >
												<option value="yes" <?php selected( $wsp_cho_display_image, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wc-sales-up' ); ?></option>
												<option value="no" <?php selected( $wsp_cho_display_image, 'no' ); ?>><?php esc_html_e( 'no', 'wc-sales-up' ); ?></option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Display Price?', 'wc-sales-up' ); ?></label>
											<select id="wsp_cho_display_price" name="wsp_cho[display_price]" >
												<option value="yes" <?php selected( $wsp_cho_display_price, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wc-sales-up' ); ?></option>
												<option value="no" <?php selected( $wsp_cho_display_price, 'no' ); ?>><?php esc_html_e( 'no', 'wc-sales-up' ); ?></option>
											</select>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<table class="wsp-table wsp_preview_tab wsp_50 last">
							<tbody>								<tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Display Position', 'wc-sales-up' ); ?></label>
											<select id="wsp_cho_display_position" name="wsp_cho[display_position]" >
												<option value="before_order_button" <?php selected( $wsp_cho_display_position, 'before_order_button' ); ?>><?php esc_html_e( 'Before Place Order Button', 'wc-sales-up' ); ?></option>
												<option value="after_order_button" <?php selected( $wsp_cho_display_position, 'after_order_button' ); ?>><?php esc_html_e( 'After Place Order Button', 'wc-sales-up' ); ?></option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Display Link of product on image?', 'wc-sales-up' ); ?></label>
											<select id="wsp_cho_display_link" name="wsp_cho[display_link]" >
												<option value="yes" <?php selected( $wsp_cho_display_link, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wc-sales-up' ); ?></option>
												<option value="no" <?php selected( $wsp_cho_display_link, 'no' ); ?>><?php esc_html_e( 'no', 'wc-sales-up' ); ?></option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Box width (For Cart Page Only)', 'wc-sales-up' ); ?></label>
											<input type="text" name="wsp_cho[box_width]" value="<?php echo (float)$wsp_cho_box_width; ?>" />
											<select id="wsp_cho_box_width_pre" name="wsp_cho[box_width_pre]" >
												<option value="percent" <?php selected( $wsp_cho_box_width_pre, 'percent' ); ?>><?php esc_html_e( '%', 'wc-sales-up' ); ?></option>
												<option value="px" <?php selected( $wsp_cho_box_width_pre, 'px' ); ?>><?php esc_html_e( 'px', 'wc-sales-up' ); ?></option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Offer title', 'sales-up-pro' ); ?></label>
											<input type="text" id="wsp_cho_title_t" value="<?php echo esc_attr( $wsp_cho_title ); ?>" name="wsp_cho[title]" />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class='wsp_field'>
											<label class="sublabel"><?php esc_html_e( 'Offer content', 'sales-up-pro' ); ?></label>
											<textarea rows='7' id="wsp_cho_content_t" name="wsp_cho[content]"><?php echo esc_html( $wsp_cho_content ); ?></textarea>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="wsp_preview_c wsp_preview_cc">
							<label><?php esc_html_e( 'Preview', 'wc-sales-up' ); ?></label>
							<div class="wsp_preview" id="wsp_preview_c">
								<div class="wsp_cho_title wsp_cho_title_c">
									<label id="wsp_cho_title"><?php echo esc_attr( $wsp_cho_title ); ?></label>
								</div>
								<div class="wsp_cho_content">
									<div class="wsp_c_img_cover" id="wsp_c_img_cover_c">
										<span class="wsp_helper"></span>
										<img class="wsp_c_img_src" id="wsp_c_img_src_c" src="<?php echo esc_url( $wsp_cho_img_src ); ?>" />
										<span class="wsp_c_img_edit dashicons dashicons-edit" id="wsp_c_img_edit_c"></span>
										<input type="hidden" id="wsp_cho_img" name="wsp_cho[img]" value="<?php echo esc_attr( $wsp_cho_img ); ?>" />
									</div>
									<div class="wsp_c_content_cover">
										<div id="wsp_cho_content"><?php echo wp_kses( $wsp_cho_content, wsp_args_kses() ); ?></div>
										<div class="wsp_cho_price" id="wsp_cho_price">
											<del><span>0.00$</span></del> <span>0.00$</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li id="tab4" class="wsp-content typography">
						<a href='https://viritte.com/downloads/sales-up-pro/' target='_blank'>
							<img style="max-width: 100%;" src='<?php echo $offer_image; ?>' alt="This feature is available in Pro version of plugin" />
						</a>
					</li>
				</ul>
			</div>
			<!--/ tabs -->
	</div>
</div>
