=== AffiliateWP Checkout Referrals ===
Contributors: sumobi, mordauk
Tags: AffiliateWP, affiliate, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, referrals, easy digital downloads, digital downloads, woocommerce, woo
Requires at least: 3.9
Tested up to: 4.7.4
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow customers to select who should receive a commission at checkout

== Description ==

> This plugin requires [AffiliateWP](https://affiliatewp.com/ "AffiliateWP") in order to function.

AffiliateWP Checkout Referrals allows a customer to award commission to an affiliate at checkout. This can be done via a select menu or input field. If an affiliate is already being tracked by the customer the affiliate select menu or input field is not shown at checkout.

**Currently supported integrations**

1. Easy Digital Downloads
2. WooCommerce

**Features:**

1. Shows a select menu or input field at checkout (but only when a referral link is not used) that allows a customer to select/enter an affiliate that their purchase will be credited to. The input field allows either an affiliate ID or username to be entered.
1. Adds a payment note to the order screen showing the referral ID, amount recorded for affiliate, and affiliate's name.
1. Optionally require that the customer select or enter an affiliate at checkout.
1. Select how the Affiliate's should be displayed in the select menu.
1. Select what text is shown above the select menu at checkout.

**What is AffiliateWP?**

[AffiliateWP](https://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

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

= 1.0.4 =
* Fix: A scenario where if the AffiliateWP settings were not saved after installing Checkout Referrals, the select menu at checkout wouldn't show affiliates correctly.

= 1.0.3 =
* New: Affiliate Selection Method. An input field can now be shown instead of a select menu. This allows a customer to enter either an affiliate ID or username.

= 1.0.2 =
* Fix: Tracked affiliate coupons were not working when checkout referrals was active

= 1.0.1 =
* Tweak: Improved the way referrals are created

= 1.0 =
* Initial release
