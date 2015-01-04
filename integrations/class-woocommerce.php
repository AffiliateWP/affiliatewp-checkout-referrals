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

		// create referral
		add_action( 'woocommerce_order_status_completed', array( $this, 'mark_referral_complete' ), 10 );

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

	/**
	 * Increase affiliate's referral count on completed purchase
	 *
	 * @param int $payment_id Payment ID
	 * @return  void
	 * @since  1.0
	 */
	public function mark_referral_complete( $order_id = 0 ) {

		// get WooCommerce order
		$order = new WC_Order( $order_id );

		// return if already tracking referral
		if ( $this->already_tracking_referral() ) {
			return;
		}

		// get user ID
		$user_id = get_post_meta( $order_id, '_affwp_checkout_referrals_user_id', true );

		if ( ! is_numeric( $user_id ) ) {
			return;
		}

		// get affiliate ID
		$affiliate    = affiliate_wp()->affiliates->get_by( 'user_id', $user_id );
		$affiliate_id = $affiliate->affiliate_id;

		$customer_email  = $order->billing_email;
		$affiliate_email = affwp_get_affiliate_email( $affiliate_id );

		// Customers cannot refer themselves
		if ( $affiliate_email == $customer_email ) {
			return; 
		}

		// get the order total
		$price = $order->get_total();

		// get the order description
		$description = $this->referral_description( $order_id );

		// calculate the referral amount
		$amount = affwp_calc_referral_amount( $price, $affiliate_id );

		$args = array(
			'amount'       => $amount,
			'reference'    => $order_id,
			'description'  => $description,
			'affiliate_id' => $affiliate_id,
			'context'      => $this->context,
			'status'       => 'unpaid'
		);

		$referral_id = $this->complete_referral( $args );

		// add payment note
		if ( $referral_id ) {

			$amount = affwp_currency_filter( affwp_format_amount( $amount ) );
			$name   = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

			$order->add_order_note( sprintf( __( 'Referral #%d for %s recorded for %s', 'affiliatewp-checkout-referrals' ), $referral_id, $amount, $name ) );

		}

	}


}
new AffiliateWP_Checkout_Referrals_WooCommerce;