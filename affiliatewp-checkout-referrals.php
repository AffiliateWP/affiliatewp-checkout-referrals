<?php
/**
 * Plugin Name: AffiliateWP - Checkout Referrals
 * Plugin URI: https://affiliatewp.com/add-ons/official-free/checkout-referrals/
 * Description: Allows a customer to award a referral to a specific affiliate at checkout.
 * Author: AffiliateWP
 * Author URI: https://affiliatewp.com
 * Version: 1.0.4
 * Text Domain: affiliatewp-checkout-referrals
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Checkout Referrals
 * @category Core
 * @author Andrew Munro
 * @version 1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Checkout_Referrals' ) ) {

	final class AffiliateWP_Checkout_Referrals {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Checkout_Referrals exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * The plugin directory variable
		 * @since  1.0
		 */
		public static $plugin_dir;

		/**
		 * The plugin URL variable
		 * @since  1.0
		 */
		public static $plugin_url;

		/**
		 * The version variable
		 * @since  1.0
		 */
		private static $version;

		/**
		 * The integrations handler instance variable
		 *
		 * @var Affiliate_WP_Checkout_Referrals_Base
		 * @since 1.0
		 */
		public $integrations;

		/**
		 * Main AffiliateWP_Checkout_Referrals Instance
		 *
		 * Insures that only one instance of AffiliateWP_Checkout_Referrals exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @static var array $instance
		 * @return The one true AffiliateWP_Checkout_Referrals
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Checkout_Referrals ) ) {
				self::$instance = new AffiliateWP_Checkout_Referrals;

				self::$plugin_dir = plugin_dir_path( __FILE__ );
				self::$plugin_url = plugin_dir_url( __FILE__ );
				self::$version    = '1.0.4';

				self::$instance->load_textdomain();
				self::$instance->hooks();
				self::$instance->includes();

				self::$instance->integrations = new Affiliate_WP_Checkout_Referrals_Base;
			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-checkout-referrals' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-checkout-referrals' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			do_action( 'affwp_checkout_referrals_setup_actions' );
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			if ( is_admin() ) {
				require_once self::$plugin_dir . 'includes/class-admin.php';
			}

			require_once self::$plugin_dir . 'integrations/class-base.php';
			require_once self::$plugin_dir . 'includes/functions.php';

			// Load the class for each integration enabled
			foreach ( affiliate_wp()->integrations->get_enabled_integrations() as $filename => $integration ) {

				if ( file_exists( self::$plugin_dir . 'integrations/class-' . $filename . '.php' ) ) {
					require_once self::$plugin_dir . 'integrations/class-' . $filename . '.php';
				}

			}

		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( plugin_dir_path( __FILE__ ) ) ) . '/languages/';
			$lang_dir = apply_filters( 'affwp_checkout_referrals_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-checkout-referrals' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'affiliatewp-checkout-referrals', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-checkout-referrals/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-checkout-referrals folder
				load_textdomain( 'affiliatewp-checkout-referrals', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-checkout-referrals/languages/ folder
				load_textdomain( 'affiliatewp-checkout-referrals', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-checkout-referrals', false, $lang_dir );
			}

		}


		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-checkout-referrals' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'Get add-ons', 'affiliatewp-checkout-referrals' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}

	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Checkout_Referrals
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_checkout_referrals = affiliatewp_checkout_referrals(); ?>
	 *
	 * @since 1.0
	 * @return object The one true AffiliateWP_Checkout_Referrals Instance
	 */
	function affiliatewp_checkout_referrals() {
	    if ( ! class_exists( 'Affiliate_WP' ) ) {
	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	    } else {
	        return AffiliateWP_Checkout_Referrals::instance();
	    }
	}
	add_action( 'plugins_loaded', 'affiliatewp_checkout_referrals', 10 );

}
