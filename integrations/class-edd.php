<?php

class AffiliateWP_Checkout_Referrals_EDD extends Affiliate_WP_Checkout_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'edd';

		// list affiliates at checkout for EDD
		add_action( 'edd_purchase_form_after_cc_form', array( $this, 'show_select_or_input' ) );

		// check the affiliate field
		add_action( 'edd_checkout_error_checks', array( $this, 'check_affiliate_field' ), 10, 2 );
		add_action( 'edd_insert_payment', array( $this, 'set_selected_affiliate' ), 1, 2 );

	}

	/**
	 * Set selected affiliate
	 *
	 * @return  void
	 * @since  1.0.1
	 */
	public function set_selected_affiliate( $payment_id = 0, $payment_data = array() ) {

		if ( $this->already_tracking_referral() ) {
			return;
		}

		add_filter( 'affwp_was_referred', '__return_true' );
		add_filter( 'affwp_get_referring_affiliate_id', array( $this, 'set_affiliate_id' ), 10, 3 );

	}

	/**
	 * Check that an affiliate has been selected
	 * @param  array $valid_data valid data
	 * @param  array $post posted data
	 * @return void
	 * @since  1.0
	 */
	public function check_affiliate_field( $valid_data, $post ) {

		// no need to check affiliate if already tracking affiliate
		if ( $this->already_tracking_referral() ) {
			return;
		}

		// Check if there's any errors
		if ( $this->get_error( $post[ $this->context . '_affiliate'] ) ) {
			edd_set_error( 'invalid_affiliate', $this->get_error( $post[ $this->context . '_affiliate'] ) );
		}

	}

}
new AffiliateWP_Checkout_Referrals_EDD;
