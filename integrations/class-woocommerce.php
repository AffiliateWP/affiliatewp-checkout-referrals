<?php

class AffiliateWP_Checkout_Referrals_WooCommerce extends Affiliate_WP_Checkout_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function init() {

		$this->context = 'woocommerce';

		// list affiliates at checkout
		add_action( 'woocommerce_after_order_notes', array( $this, 'affiliate_select_or_input' ) );

		// make field required
		add_action( 'woocommerce_checkout_process', array( $this, 'check_affiliate_field' ) );

		// Set selected affiliate.
		if ( version_compare( AFFILIATEWP_VERSION, '2.1.8', '>=' ) ) {
			// AffiliateWP v2.1.8 introduced woocommerce_checkout_update_order_meta which is used to insert a pending referral.
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'set_selected_affiliate' ), 0, 2 );
		} else {
			// AffiliateWP v2.1.7 and lower used woocommerce_checkout_order_processed
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_selected_affiliate' ), 0, 2 );
		}

	}

	/**
	 * Set selected affiliate
	 *
	 * @return  void
	 * @since  1.0.1
	 */
	public function set_selected_affiliate( $order_id = 0, $posted ) {

		if ( $this->already_tracking_referral() ) {
			return;
		}

		add_filter( 'affwp_was_referred', '__return_true' );
		add_filter( 'affwp_get_referring_affiliate_id', array( $this, 'set_affiliate_id' ), 10, 3 );

	}

	/**
	 * Check affiliate select menu
	 * @since 1.0
	 */
	public function check_affiliate_field() {

		if ( $this->already_tracking_referral() ) {
			return;
		}

		// Check if there's any errors
		if ( $this->get_error( $_POST[ $this->context . '_affiliate'] ) ) {
			wc_add_notice( $this->get_error( $_POST[ $this->context . '_affiliate'] ), 'error' );
		}

	}

	/**
	 * List affiliates
	 * @since  1.0
	 */
	public function affiliate_select_or_input( $checkout ) {

 		// return is affiliate ID is being tracked
 		if ( $this->already_tracking_referral() ) {
			return;
		}

		$description  = affwp_cr_checkout_text();
		$required     = affwp_cr_require_affiliate();

		// get affiliate list
		$affiliate_list = $this->get_affiliates();

		$required    = $required ? ' <abbr title="required" class="required">*</abbr>' : '';

		if ( 'input' === $this->get_affiliate_selection() ) : // input menu ?>

			<?php if ( $description ) : ?>
			<label for="woocommerce-affiliate"><?php echo esc_attr( $description ) . $required; ?></label>
			<?php endif; ?>

			<input type="text" id="woocommerce-affiliate" name="woocommerce_affiliate" />

		<?php else : // select menu

		if ( $affiliate_list ) {

			$affiliates = $this->get_affiliates_select_list( $affiliate_list );

			woocommerce_form_field( 'woocommerce_affiliate',
				array(
						'type'    => 'select',
						'class'   => array( 'form-row-wide' ),
						'label'   => $description . $required,
						'options' => $affiliates
				),
				$checkout->get_value( 'woocommerce_affiliate' )
			);

		}

		endif;

	}

}
new AffiliateWP_Checkout_Referrals_WooCommerce;
