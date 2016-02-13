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
		add_action( 'edd_purchase_form_before_submit', array( $this, 'affiliate_select_or_input' ) );

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
	 * Set the affiliate ID
	 * Overrides a tracked affiliate coupon
	 *
	 * @return  void
	 * @since  1.0.1
	 */
	public function set_affiliate_id( $affiliate_id ) {

		$affiliate_selection = $this->get_affiliate_selection();

		// Input field. Accepts either an affiliate ID or username
		if ( 'input' === $affiliate_selection ) {

			if ( isset( $_POST['edd_affiliate'] ) && $_POST['edd_affiliate'] ) {

				if ( absint( $_POST['edd_affiliate'] ) ) {

					// affiliate ID
					$affiliate_id = absint( $_POST['edd_affiliate'] );

				} elseif ( ! is_numeric( $affiliate_id ) ) {

					// get affiliate ID from username
					$user = get_user_by( 'login', sanitize_text_field( urldecode( $_POST['edd_affiliate'] ) ) );

					if ( $user ) {
						$affiliate_id = affwp_get_affiliate_id( $user->ID );
					}

				}

			}

		} else {

			// select menu
			if ( isset( $_POST['edd_affiliate'] ) && $_POST['edd_affiliate'] ) {
				$affiliate_id = absint( $_POST['edd_affiliate'] );
			}

		}

		return $affiliate_id;
	}

	/**
	 * Show affiliate dropdown at checkout
	 *
	 * @return  void
	 * @since  1.0
	 */
	public function affiliate_select_or_input() {

		if ( $this->already_tracking_referral() ) {
		 	return;
		}

		// get affiliate list
		$affiliate_list = $this->get_affiliates();

		$description         = affiliate_wp()->settings->get( 'checkout_referrals_checkout_text' );
		$display             = affiliate_wp()->settings->get( 'checkout_referrals_affiliate_display' );

		?>

		<p>
			<?php if ( $description ) : ?>
			<label for="edd-affiliate"><?php echo esc_attr( $description ); ?></label>
			<?php endif; ?>

			<?php if ( 'input' === $this->get_affiliate_selection() ) : // input menu ?>

				<input type="text" id="edd-affiliate" name="edd_affiliate" />

			<?php else : // select menu ?>

				<select id="edd-affiliate" name="edd_affiliate" class="edd-select">

				<option value="0"><?php _e( 'Select', 'affiliatewp-checkout-referrals' ); ?></option>
				<?php foreach ( $affiliate_list as $affiliate_id => $user_id ) :
					$user_info = get_userdata( $user_id );
				?>
					<option value="<?php echo $affiliate_id; ?>"><?php echo $user_info->$display; ?></option>
				<?php endforeach; ?>
				</select>

			<?php endif; ?>

		</p>

	<?php
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
		if ( $this->get_error( $post['edd_affiliate'] ) ) {
			edd_set_error( 'invalid_affiliate', $this->get_error( $post['edd_affiliate'] ) );
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
