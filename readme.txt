=== WooChimpCommerce  ===
Contributors: hiren1612
Donate link: http://www.hikebranding.com/
Tags: woocommerce, mailchimp, order, mailchimp subscriber, checkout, cart, product, category, custom field
Requires at least: 4.1
Tested up to: 5.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin which allows you to add MailChimp subscription option on the WooCommerce checkout page.

== Description ==

WooChimpCommerce plugin will provide to add MailChimp subscriber option on the checkoutpage. Place a shortcode in page, post, text widget or template files to display in front-end. It's that simple.

1. Tick checkbox in plugin setting tabs for display subscriber option on checkout page.
2. Plugin's provide shortcode for subscriber form.

= Usage =

Place this auto generated shortcode in page, post or text widget where you'd like to display posts.

`
[WooChimpCommerce id=e2f9e9764f]
`

= Templates =

Place this shortcode in any template parts of your theme.

`
<?php echo do_shortcode('[WooChimpCommerce id=e2f9e9764f]'); ?>
`

== Installation ==
1. Upload "woochimpcommerce" to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.

= How to Use =
1. Tick checkbox in plugin setting tabs for display subscriber option on checkout page.
2. Place shortcode [WooChimpCommerce id=e2f9e9764f] in wordpress page, post or text widget.
3. Place the code `<?php echo do_shortcode('[WooChimpCommerce id=e2f9e9764f]'); ?>` in template files.

== Frequently Asked Questions ==

= Having problems, questions, bugs & suggestions =
Contact us at http://www.hikebranding.com/


== Screenshots ==
1. API Key. Here you can add mailchimp API key for connect mailchimp account.
2. All Products. Here you can select mailchimp list for add subscriber.
3. All Categories. Here you can select mailchimp list for add subscriber.
4. Custom Field. It's mapping your checkout and mailchimp list field.
5. Short Code. It's display subscription form in Frontend.
6. Here you can set subscription option in checkout page.

== Changelog ==
= v1.0 =
* Initial release version.

= v1.1 =
* Fix admin setting issue.