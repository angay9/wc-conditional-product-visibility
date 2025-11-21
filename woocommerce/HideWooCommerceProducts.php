<?php

namespace WCConditionalProductVsibility\WooCommerce;

class HideWooCommerceProducts 
{
    public function __construct()
    {
        // Run both handlers on front-end main queries
        add_action('pre_get_posts', [$this, 'excludeProductsByCategories']);
        add_action('pre_get_posts', [$this, 'excludeProductsByTags']);

        // Run product single checks to return a 404 when product belongs to a hidden category/tag
        add_action('template_redirect', [$this, 'maybeRedirectProduct404ByCategories']);
        add_action('template_redirect', [$this, 'maybeRedirectProduct404ByTags']);
    }

    /**
     * Exclude products that belong to configured product_cat term IDs.
     *
     * @param \WP_Query $query
     */
    public function excludeProductsByCategories(\WP_Query $query)
    {
        if (is_admin() || ! $query->is_main_query()) {
            return;
        }

        if (! $this->isProductListingQuery($query)) {
            return;
        }

        $opts = (array) get_option('wc_codnitional_product_visibility_settings', []);
        $cats = isset($opts['hide_products_from_categories']) ? array_filter(array_map('intval', (array) $opts['hide_products_from_categories'])) : [];

        if (empty($cats)) {
            return;
        }

        $taxQuery = (array) $query->get('tax_query') ?: [];
        if (! isset($taxQuery['relation'])) {
            $taxQuery['relation'] = 'AND';
        }

        $taxQuery[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $cats,
            'operator' => 'NOT IN',
        ];

        $query->set('tax_query', $taxQuery);
    }

    /**
     * Exclude products that belong to configured product_tag term IDs.
     *
     * @param \WP_Query $query
     */
    public function excludeProductsByTags(\WP_Query $query)
    {
        if (is_admin() || ! $query->is_main_query()) {
            return;
        }

        if (! $this->isProductListingQuery($query)) {
            return;
        }

        $opts = (array) get_option('wc_codnitional_product_visibility_settings', []);
        $tags = isset($opts['hide_products_from_tags']) ? array_filter(array_map('intval', (array) $opts['hide_products_from_tags'])) : [];

        if (empty($tags)) {
            return;
        }

        $taxQuery = (array) $query->get('tax_query') ?: [];
        if (! isset($taxQuery['relation'])) {
            $taxQuery['relation'] = 'AND';
        }

        $taxQuery[] = [
            'taxonomy' => 'product_tag',
            'field'    => 'term_id',
            'terms'    => $tags,
            'operator' => 'NOT IN',
        ];

        $query->set('tax_query', $taxQuery);
    }

    /**
     * If the current single product belongs to any configured hidden product_cat term IDs,
     * force a 404 response and render the 404 template.
     */
    public function maybeRedirectProduct404ByCategories()
    {
        if (is_admin() || ! is_singular('product')) {
            return;
        }

        global $wp_query;

        if (! empty($wp_query->is_404) || $wp_query->is_404) {
            return;
        }

        $productId = get_queried_object_id();
        if (! $productId) {
            return;
        }

        $opts = (array) get_option('wc_codnitional_product_visibility_settings', []);
        $cats = isset($opts['hide_products_from_categories']) ? array_filter(array_map('intval', (array) $opts['hide_products_from_categories'])) : [];

        if (empty($cats)) {
            return;
        }

        foreach ($cats as $termId) {
            if (has_term($termId, 'product_cat', $productId)) {
                $wp_query->set_404();
                status_header(404);
                nocache_headers();
                // Render 404 template and exit
                include( get_query_template('404') );
                exit;
            }
        }
    }

    /**
     * If the current single product belongs to any configured hidden product_tag term IDs,
     * force a 404 response and render the 404 template.
     */
    public function maybeRedirectProduct404ByTags()
    {
        if (is_admin() || ! is_singular('product')) {
            return;
        }

        global $wp_query;

        if (! empty($wp_query->is_404) || $wp_query->is_404) {
            return;
        }

        $productId = get_queried_object_id();
        if (! $productId) {
            return;
        }

        $opts = (array) get_option('wc_codnitional_product_visibility_settings', []);
        $tags = isset($opts['hide_products_from_tags']) ? array_filter(array_map('intval', (array) $opts['hide_products_from_tags'])) : [];

        if (empty($tags)) {
            return;
        }

        foreach ($tags as $termId) {
            if (has_term($termId, 'product_tag', $productId)) {
                $wp_query->set_404();
                status_header(404);
                nocache_headers();
                include( get_query_template('404') );
                exit;
            }
        }
    }

    /**
     * Determine whether the given query is a front-end product listing (shop, product archives, product search, tax archives).
     *
     * @param \WP_Query $query
     * @return bool
     */
    private function isProductListingQuery(\WP_Query $query)
    {
        $postType = $query->get('post_type');

        return (
            'product' === $postType
            || is_post_type_archive('product')
            || function_exists('is_shop') && is_shop()
            || is_tax('product_cat')
            || is_tax('product_tag')
            || (empty($postType) && (is_search() || is_archive()))
        );
    }
}