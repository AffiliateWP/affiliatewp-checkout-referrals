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

}
