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
	 * Completes a referral. Used when orders are marked as completed
	 *
	 * @access  public
	 * @since   1.0
	 * @param   $reference The reference column for the referral to complete per the current context
	 * @return  bool
	 */
	public function complete_referral( $args = array() ) {
		
		$referral_id = affiliate_wp()->referrals->add( 
			array(
				'amount'       => $args['amount'],
				'reference'    => $args['reference'],
				'description'  => $args['description'],
				'affiliate_id' => $args['affiliate_id'],
				'context'      => $this->context,
				'status'       => $args['status']   
			) 
		);

		if ( $referral_id ) {
			return $referral_id;
		}

		return false;
		
	}

}