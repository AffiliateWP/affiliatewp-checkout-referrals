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
		add_action( 'edd_purchase_form_before_submit', array( $this, 'show_select_or_input' ) );

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
		add_filter( 'affwp_get_referring_affiliate_id', array( $this, 'set_affiliate_id' ) );

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

	/**
	 * Referral description
	 * @return string The referral's description
	 */
	public function referral_description( $payment_id = 0 ) {
		// description
		$description = '';
		$downloads   = edd_get_payment_meta_downloads( $payment_id );

		foreach ( $downloads as $key => $item ) {
			$description .= get_the_title( $item['id'] );
			if ( $key + 1 < count( $downloads ) ) {
				$description .= ', ';
			}
		}

		return $description;
	}

}
new AffiliateWP_Checkout_Referrals_EDD;
