<?php

class Affiliate_WP_Checkout_Referrals_Base {

	public $context;

	/**
	 * Plugin Title
	 */
	public $title = 'AffiliateWP Checkout Referrals';

	public function __construct() {
		$this->init();
	}

	/**
	 * Gets things started
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
	 */
	public function init() {}

	/**
	 * Check to see if user is already tracking a referral link in their cookies
	 *
	 * @return boolean true if tracking affiliate, false otherwise
	 * @since  1.0
	 */
	public function already_tracking_referral() {
		return affiliate_wp()->tracking->was_referred();
	}

	/**
	 * Get an array of affiliates
	 * @return array Affiliate IDs and their corresponding User IDs.
	 */
	public function get_affiliates() {

		// get all active affiliates
		$affiliates = affiliate_wp()->affiliates->get_affiliates(
			array(
				'status' => 'active',
				'number' => -1
			)
		);

		$affiliate_list = array();

		if ( $affiliates ) {
			foreach ( $affiliates as $affiliate ) {
				$affiliate_list[ $affiliate->affiliate_id ] = $affiliate->user_id;
			}
		}

		return $affiliate_list;
	}

	/**
	 * Get affiliate selection
	 * @since 1.0.3
	 */
	public function get_affiliate_selection() {

		$affiliate_selection = affiliate_wp()->settings->get( 'checkout_referrals_affiliate_selection' );

		return $affiliate_selection;
	}

	/**
	 * Validates an affiliate
	 *
	 * @since 1.0.3
	 * @param $affiliate $affiliate username or ID of affiliate
	 */
	public function is_valid_affiliate( $affiliate = '' ) {

		// set flag to false
		$valid_affiliate = false;

		if ( is_numeric( $affiliate ) ) {

			// affiliate ID provided
			if ( affwp_is_active_affiliate( $affiliate ) ) {
				$valid_affiliate = true;
			}

		} else {

			// username provided. Uppercase or lowercase usernames are ok
			if ( affwp_is_active_affiliate( affiliate_wp()->tracking->get_affiliate_id_from_login( $affiliate ) ) ) {
				$valid_affiliate = true;
			}

		}

		return $valid_affiliate;
	}

	/**
	 * Error messages
	 *
	 * @since 1.0.3
	 */
	public function get_error( $affiliate = '' ) {

		// Whether an affiliate is required to be selected or entered
		$require_affiliate = affiliate_wp()->settings->get( 'checkout_referrals_require_affiliate' );

		// either input or select menu
		$affiliate_selection = $this->get_affiliate_selection();

		// the affiliate that was submitted
		$affiliate_submitted = isset( $affiliate ) && $affiliate ? $affiliate : '';

		$error = '';
		
		/**
		 * Affiliate is required but not affiliate was selected/entered
		 */
		if ( $require_affiliate && ! $affiliate_submitted ) {

			if ( 'input' === $affiliate_selection ) {
				// input field
				$error = __( 'Please enter an affiliate', 'affiliatewp-checkout-referrals' );

			} else {
				// select menu
				$error = __( 'Please select an affiliate', 'affiliatewp-checkout-referrals' );
			}

		} else {

			/**
			 * Validate the affiliate submitted
			 * Set error if affiliate was submitted but the affiliate is invalid
			 */

			if ( $affiliate_submitted && ! $this->is_valid_affiliate( $affiliate_submitted ) ) {
				$error = __( 'Please enter a valid affiliate', 'affiliatewp-checkout-referrals' );
			}

		}

		if ( $error ) {
			return apply_filters( 'affwp_checkout_referrals_require_affiliate_error', $error );
		} else {
			return false;
		}

	}

}
