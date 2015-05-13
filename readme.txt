=== AffiliateWP Checkout Referrals ===
Contributors: sumobi, mordauk
Tags: AffiliateWP, affiliate, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, referrals, easy digital downloads, digital downloads, woocommerce, woo
Requires at least: 3.9
Tested up to: 4.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow customers to select who should receive a commission at checkout

== Description ==

> This plugin requires [AffiliateWP](http://affiliatewp.com/ "AffiliateWP") in order to function.

AffiliateWP Checkout Referrals allows a customer to select an affiliate at checkout to receive commission from their purchase. It's especially useful for sites whose affiliates are NPO (Nonprofit organizations). A customer can select the NPO from checkout, and that NPO will receive a commission on the sale. If an affiliate link is already being tracked (Eg /?ref=123, the affiliate list is not shown at checkout.

**Currently supported integrations**

1. Easy Digital Downloads
2. WooCommerce

**Features:**

1. Shows a select menu at checkout (but only when a referral link is not used) that allows a customer to select an affiliate that their purchase will be credited to.
1. Adds a payment note to the order screen showing the referral ID, amount recorded for affiliate, and affiliate's name
1. Optionally require that the customer select an affiliate at checkout
1. Select how the Affiliate's should be displayed in the select menu
1. Select what text is shown above the select menu at checkout

**What is AffiliateWP?**

[AffiliateWP](http://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin
1. Configure the options from Affiliates &rarr; Settings &rarr; Integrations

OR you can just install it with WordPress by going to Plugins &rarr; Add New &rarr; and type this plugin's name

== Screenshots ==

1. The add-ons's settings from Affiliates &rarr; Settings &rarr; Integrations
1. The select menu at checkout that a customer can use to award a commission to an affiliate

== Upgrade Notice ==
Fix: Tracked affiliate coupons were not working when checkout referrals was active

== Changelog ==

= 1.0.2 =
* Fix: Tracked affiliate coupons were not working when checkout referrals was active

= 1.0.1 =
* Tweak: Improved the way referrals are created

= 1.0 =
* Initial release