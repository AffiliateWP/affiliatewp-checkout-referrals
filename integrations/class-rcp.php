<?php

class AffiliateWP_Checkout_Referrals_RCP extends Affiliate_WP_Checkout_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function init() {

		$this->context = 'rcp';

		// list affiliates
		add_action( 'rcp_before_registration_submit_field', array( $this, 'show_select_or_input' ) );
		add_action( 'rcp_before_form_errors', array( $this, 'check_affiliate_field' ), 10, 1 );
		add_action( 'rcp_form_processing', array( $this, 'set_selected_affiliate' ), 1, 3 );
	}


	/**
	 * Set selected affiliate
	 *
	 * @return  void
	 * @since  1.1
	 */
	public function set_selected_affiliate( $post, $user_id, $price ) {

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
	 * @since  1.1
	 */
	public function check_affiliate_field( $post ) {

		// no need to check affiliate if already tracking affiliate
		if ( $this->already_tracking_referral() ) {
			return;
		}

		// Check if there's any errors
		if ( $this->get_error( $post[ $this->context . '_affiliate'] ) ) {
			rcp_errors()->add( 'invalid_affiliate', $this->get_error( $post[ $this->context . '_affiliate'] ), 'register' );
		}

	}

}
new AffiliateWP_Checkout_Referrals_RCP;
