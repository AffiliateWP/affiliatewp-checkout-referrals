<?php

class AffiliateWP_Checkout_Referrals_Admin {
	
	public function __construct() {
		// settings
		add_filter( 'affwp_settings_integrations', array( $this, 'settings' ) );
	}

	/**
	 * Option to globally allow all affiliates to access order details
	 *
	 * @since 1.0
	 * @return boolean
	 */
	public function settings( $fields ) {
		
		$fields['checkout_referrals_header'] = array(
			'name' => __( 'Checkout Referrals', 'affiliatewp-checkout-referrals' ),
			'type' => 'header',
		);

		$fields['checkout_referrals_checkout_text'] = array(
			'name' => __( 'Checkout Text', 'affiliatewp-checkout-referrals' ),
			'desc' => '<p class="description">' . __( 'Enter the text that is shown with the affiliate select menu at checkout', 'affiliatewp-checkout-referrals' ) . '</p>',
			'type' => 'text',
			'std'  => __( 'Select the affiliate that should be awarded a commission for this purchase', 'affiliatewp-checkout-referrals' )
		);

		$fields['checkout_referrals_require_affiliate'] = array(
			'name' => __( 'Require Affiliate Selection', 'affiliatewp-checkout-referrals' ),
			'desc' => __( 'Customer must select an Affiliate to award the referral to', 'affiliatewp-checkout-referrals' ),
			'type' => 'checkbox'
		);

		$fields['checkout_referrals_affiliate_display'] = array(
			'name' => __( 'Affiliate Display', 'affiliatewp-checkout-referrals' ),
			'desc' => __( 'How the Affiliates will be displayed at checkout', 'affiliatewp-checkout-referrals' ),
			'type' => 'radio',
			'options' => array(
				'user_nicename' => 'User Nicename',
				'display_name' => 'Display Name',
				'nickname'     => 'Nickname'
			),
			'std' => 'user_nicename'
		);

		return $fields;
	}

}
$affiliatewp_admin = new AffiliateWP_Checkout_Referrals_Admin;