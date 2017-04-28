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
	 * Show affiliate select menu or input field
	 *
	 * @return  void
	 * @since  1.0.3
	 */
	public function show_select_or_input() {

		if ( $this->already_tracking_referral() ) {
		 	return;
		}

		// get affiliate list
		$affiliate_list = $this->get_affiliates();

		$description  = affwp_cr_checkout_text();
		$display      = affwp_cr_affiliate_display();
		$required     = affwp_cr_require_affiliate();

		$required_html = '';

		if ( $required ) {
			switch ( $this->context ) {
				case 'edd':
					$required_html = ' <span class="edd-required-indicator">*</span>';
					break;
			}
		}

		$required     = $required ? ' <abbr title="required" class="required">*</abbr>' : '';

		?>

		<p>
			<?php if ( $description ) : ?>
			<label for="<?php echo $this->context;?>-affiliate"><?php echo esc_attr( $description ); ?><?php echo $required_html; ?></label>
			<?php endif; ?>

			<?php if ( 'input' === $this->get_affiliate_selection() ) : // input menu ?>

				<input type="text" id="<?php echo $this->context; ?>-affiliate" name="<?php echo $this->context;?>_affiliate" />

			<?php else : // select menu ?>

				<select id="<?php echo $this->context;?>-affiliate" name="<?php echo $this->context;?>_affiliate" class="<?php echo $this->context;?>-select">

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
	 * Set the affiliate ID
	 * This overrides a tracked affiliate coupon
	 *
	 * @return  void
	 * @since  1.0.1
	 */
	public function set_affiliate_id( $affiliate_id ) {

		$context          = $this->context;
		$posted_affiliate = $_POST[ $context . '_affiliate'];

		$affiliate_selection = $this->get_affiliate_selection();

		// Input field. Accepts either an affiliate ID or username
		if ( 'input' === $affiliate_selection ) {

			if ( isset( $posted_affiliate ) && $posted_affiliate ) {

				if ( absint( $posted_affiliate ) ) {

					// affiliate ID
					$affiliate_id = absint( $posted_affiliate );

				} elseif ( ! is_numeric( $affiliate_id ) ) {

					// get affiliate ID from username
					$user = get_user_by( 'login', sanitize_text_field( urldecode( $posted_affiliate ) ) );

					if ( $user ) {
						$affiliate_id = affwp_get_affiliate_id( $user->ID );
					}

				}

			}

		} else {

			// select menu
			if ( isset( $posted_affiliate ) && $posted_affiliate ) {
				$affiliate_id = absint( $posted_affiliate );
			}

		}

		return $affiliate_id;
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
