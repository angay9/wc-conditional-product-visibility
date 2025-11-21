=== WC Conditional Product Visibility ===
Contributors: Andriy Haydash
Tags: woocommerce, products, visibility, category, tag, catalog
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Hide WooCommerce products from listing pages and single product pages when they belong to selected categories or tags.

== Description ==
WC Conditional Product Visibility lets site administrators hide products across the store by selecting product categories or tags in the plugin settings. Selected items will be excluded from frontend product listings and will return a 404 on single product pages when applicable.

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to "WC Conditional Product Visibility" in the admin menu.
4. Select categories and/or tags to hide and save.

== Screenshots ==
1. assets/static/img/preview-1.png - Settings page (select categories & tags)
2. assets/static/img/preview-2.png - Admin product list preview
3. assets/static/img/preview-3.png - Frontend store view (hidden products excluded)
4. assets/banner-772x250.png - Plugin banner

== Frequently Asked Questions ==
= Will hidden products be removed from search and archives? =
Yes. Products in selected categories/tags are excluded from listing queries and search results.

= What happens if I open a direct product URL for a hidden product? =
The plugin forces a 404 response for single product pages when the product belongs to a hidden category or tag.

== Changelog ==
= 1.0.0 =
* Initial release: hide products by category and tag from listings and single product pages.

== Upgrade Notice ==
= 1.0.0 =
Initial release.