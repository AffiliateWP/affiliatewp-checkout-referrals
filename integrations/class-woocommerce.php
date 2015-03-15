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
		add_action( 'woocommerce_after_order_notes', array( $this, 'affiliate_dropdown' ) );

		// make field required
		add_action( 'woocommerce_checkout_process', array( $this, 'check_affiliate_field' ) );

		// update order meta
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );

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
	 * Set the affiliate ID
	 *
	 * @return  void
	 * @since  1.0.1
	 */
	public function set_affiliate_id( $affiliate_id ) {
		$affiliate_id = isset( $_POST['affwp-checkout-referrals-affiliates'] ) ? affwp_get_affiliate_id( absint( $_POST['affwp-checkout-referrals-affiliates'] ) ) : '';

		return $affiliate_id;
	}

	/**
	 * Check affiliate select menu
	 * @since 1.0
	 */
	public function check_affiliate_field() {

		if ( $this->already_tracking_referral() ) {
			return;
		}

		$require_affiliate = affiliate_wp()->settings->get( 'checkout_referrals_require_affiliate' );

	  	// error message
	    if ( ! ( isset( $_POST['affwp-checkout-referrals-affiliates'] ) && $_POST['affwp-checkout-referrals-affiliates'] ) && $require_affiliate ) {
	        $message = apply_filters( 'affwp_checkout_referrals_require_affiliate_error', __( 'Please select an affiliate', 'affiliatewp-checkout-referrals' ) );

	        wc_add_notice( $message, 'error' );
	    }

	}
	
	/**
	 * List affiliates
	 * @since  1.0
	 */
	public function affiliate_dropdown( $checkout ) {
		
 		// return is affiliate ID is being tracked
 		if ( $this->already_tracking_referral() ) {
			return;
		}

		// get affiliate list
		$affiliate_list = $this->get_affiliates();

		$description = affiliate_wp()->settings->get( 'checkout_referrals_checkout_text' );
		$display     = affiliate_wp()->settings->get( 'checkout_referrals_affiliate_display' );
		$required    = affiliate_wp()->settings->get( 'checkout_referrals_require_affiliate' );

		$affiliates = array( 0 => 'Select' );

		if ( $affiliate_list ) {

			// now that we've got a list of affiliate IDs and their User IDs, but out a list
		 	foreach ( $affiliate_list as $key => $affiliate ) {
		 		$user_info = get_userdata( $affiliate );

		 		$affiliates[ $affiliate ] = $user_info->$display;
		 	}
		 	
		 	$required = $required ? ' <abbr title="required" class="required">*</abbr>' : '';

		    woocommerce_form_field( 'affwp-checkout-referrals-affiliates', 
		    	array(
			        'type'    => 'select',
			        'class'   => array( 'form-row-wide' ),
			        'label'   => $description . $required,
			        'options' => $affiliates
			    ), 
			    $checkout->get_value( 'affwp-checkout-referrals-affiliates' )
			);

		}
	 	
	 
	}

	/**
	 * Update order meta
	 * @since  1.0
	 */		 
	public function update_order_meta( $order_id ) {
	    if ( ! empty( $_POST['affwp-checkout-referrals-affiliates'] ) ) {
	        update_post_meta( $order_id, '_affwp_checkout_referrals_user_id', $_POST['affwp-checkout-referrals-affiliates'] );
	    }
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