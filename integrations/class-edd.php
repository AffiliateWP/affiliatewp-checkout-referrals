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
		add_action( 'edd_purchase_form_before_submit', array( $this, 'affiliate_dropdown' ) );

		// create referral
		add_action( 'edd_complete_purchase', array( $this, 'mark_referral_complete' ) );

		// check the affiliate field
		add_action( 'edd_checkout_error_checks', array( $this, 'check_affiliate_field' ), 10, 2 );

	}

	/**
	 * Show affiliate dropdown at checkout
	 *
	 * @return  void
	 * @since  1.0
	 */
	public function affiliate_dropdown() {

		if ( $this->already_tracking_referral() ) {
			return;
		}

		// get affiliate list
		$affiliate_list = $this->get_affiliates();

		$description = affiliate_wp()->settings->get( 'checkout_referrals_checkout_text' );
		$display     = affiliate_wp()->settings->get( 'checkout_referrals_affiliate_display' );

	?>

		<p>
			<?php if ( $description ) : ?>
			<label for="edd-affiliate"><?php echo esc_attr( $description ); ?></label>
			<?php endif; ?>

			<select id="edd-affiliate" name="edd_affiliate" class="edd-select">
			
			<option name=""><?php _e( 'Select', 'affiliatewp-checkout-referrals' ); ?></option>
			<?php foreach ( $affiliate_list as $key => $affiliate ) : 
				$user_info = get_userdata( $affiliate );
			?>
				<option value="<?php echo $affiliate; ?>"><?php echo $user_info->$display; ?></option>
			<?php endforeach; ?>
			</select>
		</p>

	<?php }

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

	/**
	 * Increase affiliate's referral count on completed purchase
	 *
	 * @param int $payment_id Payment ID
	 * @return  void
	 * @since  1.0
	 */
	public function mark_referral_complete( $payment_id ) {

		// return if already tracking referral
		if ( $this->already_tracking_referral() ) {
			return;
		}

		$payment_meta     = edd_get_payment_meta( $payment_id );
		$purchase_session = edd_get_purchase_session();
		$price            = $purchase_session['price'];
		$user_id          = isset( $purchase_session['post_data']['edd_affiliate'] ) ? $purchase_session['post_data']['edd_affiliate'] : null;

		if ( ! is_numeric( $user_id ) ) {
			return;
		}

		// get affiliate ID
		$affiliate    = affiliate_wp()->affiliates->get_by( 'user_id', $user_id );
		$affiliate_id = $affiliate->affiliate_id;

		// calculate referral amount
		$amount = affwp_calc_referral_amount( $price, $affiliate_id );

		$description = $this->referral_description( $payment_id );

		$customer_email  = edd_get_payment_user_email( $payment_id );
		$affiliate_email = affwp_get_affiliate_email( $affiliate_id );

		if ( $affiliate_email == $customer_email ) {
			return; // Customers cannot refer themselves
		}

		$args = array(
			'amount'       => $amount,
			'reference'    => $payment_id,
			'description'  => $description,
			'affiliate_id' => $affiliate_id,
			'context'      => $this->context,
			'status'       => 'unpaid'
		);

		$referral_id = $this->complete_referral( $args );

	}


	/**
	 * Check that an affiliate has been selected
	 * @param  array $valid_data valid data
	 * @param  array $post posted data
	 * @return void
	 * @since  1.0
	 */
	public function check_affiliate_field( $valid_data, $post ) {
		
		if ( $this->already_tracking_referral() ) {
			return;
		}

		$require_affiliate = affiliate_wp()->settings->get( 'checkout_referrals_require_affiliate' );
		$affiliate         = isset( $post['edd_affiliate'] ) ? $post['edd_affiliate'] : '';

		if ( ! is_numeric( $affiliate ) && $require_affiliate ) {
			edd_set_error( 'invalid_affiliate', apply_filters( 'affwp_checkout_referrals_require_affiliate_error', __( 'Please select an affiliate', 'affiliatewp-checkout-referrals' ) ) );
		}
	}

}
new AffiliateWP_Checkout_Referrals_EDD;