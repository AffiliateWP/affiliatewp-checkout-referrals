<?php

class AffiliateWP_Checkout_Referrals_WooCommerce extends Affiliate_WP_Checkout_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function init() {

		$this->context = 'woocommerce';

		// list affiliates at checkout
		add_action( 'woocommerce_after_order_notes', array( $this, 'affiliate_select_or_input' ) );

		// make field required
		add_action( 'woocommerce_checkout_process', array( $this, 'check_affiliate_field' ) );

		// set selected affiliate
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_selected_affiliate' ), 0, 2 );

	}

	/**
	 * Set selected affiliate
	 *
	 * @return  void
	 * @since  1.0.1
	 */
	public function set_selected_affiliate( $order_id = 0, $posted ) {

		if ( $this->already_tracking_referral() ) {
			return;
		}

		add_filter( 'affwp_was_referred', '__return_true' );
		add_filter( 'affwp_get_referring_affiliate_id', array( $this, 'set_affiliate_id' ) );

	}

	/**
	 * Check affiliate select menu
	 * @since 1.0
	 */
	public function check_affiliate_field() {

		if ( $this->already_tracking_referral() ) {
			return;
		}

		// Check if there's any errors
		if ( $this->get_error( $_POST[ $this->context . '_affiliate'] ) ) {
			wc_add_notice( $this->get_error( $_POST[ $this->context . '_affiliate'] ), 'error' );
		}

	}

	/**
	 * List affiliates
	 * @since  1.0
	 */
	public function affiliate_select_or_input( $checkout ) {

 		// return is affiliate ID is being tracked
 		if ( $this->already_tracking_referral() ) {
			return;
		}

		// get affiliate list
		$affiliate_list = $this->get_affiliates();

		$description  = affwp_cr_checkout_text();
		$display      = affwp_cr_affiliate_display();
		$required     = affwp_cr_require_affiliate();

		$required    = $required ? ' <abbr title="required" class="required">*</abbr>' : '';

		$affiliates = array( 0 => 'Select' );

		if ( 'input' === $this->get_affiliate_selection() ) : // input menu ?>

			<?php if ( $description ) : ?>
			<label for="woocommerce-affiliate"><?php echo esc_attr( $description ) . $required; ?></label>
			<?php endif; ?>

			<input type="text" id="woocommerce-affiliate" name="woocommerce_affiliate" />

		<?php else : // select menu

		if ( $affiliate_list ) {

			// now that we've got a list of affiliate IDs and their User IDs, build out a list
		 	foreach ( $affiliate_list as $affiliate_id => $user_id ) {
		 		$user_info = get_userdata( $user_id );

		 		$affiliates[ $affiliate_id ] = $user_info->$display;
		 	}

		    woocommerce_form_field( 'woocommerce_affiliate',
		    	array(
			        'type'    => 'select',
			        'class'   => array( 'form-row-wide' ),
			        'label'   => $description . $required,
			        'options' => $affiliates
			    ),
			    $checkout->get_value( 'woocommerce_affiliate' )
			);

		}

		endif;

	}

	/**
	 * Referral description
	 * @return string The referral's description
	 */
	public function referral_description( $order_id = 0 ) {
		// get order
		$order = new WC_Order( $order_id );

		$description = array();

		$items = $order->get_items();

		foreach ( $items as $key => $item ) {
			$description[] = $item['name'];
		}

		return implode( ', ', $description );
	}

}
new AffiliateWP_Checkout_Referrals_WooCommerce;
