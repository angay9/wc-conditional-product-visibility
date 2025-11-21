<?php

namespace WCConditionalProductVsibility;

use WCConditionalProductVsibility\Settings\Settings;
use WCConditionalProductVsibility\WooCommerce\HideWooCommerceProducts;

class Plugin 
{
    /**
     * Singletons
     *
     * @var array
     */
    protected static $container = [];

    /**
     * Singleton global services
     *
     * @var array
     */
    protected static $singletons = [];

    /**
     * Plugin components
     *
     * @var array
     */
    protected $components = [
        HideWooCommerceProducts::class,
        Settings::class,
    ];

    /**
     * Required plugins
     *
     * @var array
     */
    protected $requiredPlugins = [
        'woocommerce/woocommerce.php' => 'WooCommerce',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_init', [$this, 'checkRequirements']);

        $this->initComponents();

        add_action('wp_enqueue_scripts', [$this, 'addAssets']);
        add_action('admin_enqueue_scripts', [$this, 'addAdminAssets']);

        $this->addShortcodes();
    }

    /**
     * Check if all required plugins are active
     *
     * @return boolean
     */
    public function checkRequirements()
    {
        $missingPlugins = [];

        foreach ($this->requiredPlugins as $key => $name) {

            if (! is_plugin_active($key)) {
                $missingPlugins[$key] = $name;
            }
        }

        if (empty($missingPlugins)) {
            return;
        }
            
        add_action('admin_notices', function() use ($missingPlugins) {
            $names = rtrim(implode(', ', $missingPlugins), ', ');

            printf(
                '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                __("WC Conditional Product Visibility requires <strong>{$names}</strong> to be active", 'wc-conditional-product-visibility')
            );
    
        });

        deactivate_plugins('wc-conditional-product-visibility/wc-conditional-product-visibility.php');
    }

    /**
     * Add plugin shortcodes
     *
     * @return void
     */
    public function addShortcodes()
    {
        
    }

    /**
     * Add plugin assets
     *
     * @return void
     */
    public function addAssets()
    {
        // wp_enqueue_script('app-js', plugin_dir_url(__FILE__) . 'assets/js/app.js', [
        //     'jquery',
        // ]);

        // wp_localize_script('app-js', 'AppConfig', [
        //     'nonce' => wp_create_nonce('plugin_action'),
        //     'ajax_url' => admin_url('admin-ajax.php'),
        // ]);
    }

    /**
     * Add admin assets
     *
     * @return void
     */
    public function addAdminAssets()
    {
        // Add admin assets
        wp_enqueue_style('farbtastic');
        wp_enqueue_script('farbtastic');

        // We're including the WP media scripts here because they're needed for the image upload field
        // If you're not including an image upload then you can leave this function call out
        wp_enqueue_media();

        wp_enqueue_script('wpt-admin-js', plugin_dir_url(__FILE__) . 'assets/js/settings.js', array('farbtastic', 'jquery'), '1.0.0');
    }

    /**
     * Init plugin components
     *
     * @return void
     */
    public function initComponents()
    {
        foreach ($this->components as $component) {
            new $component();
        }
    }

        /**
     * Get a singleton servce
     *
     * @param string $class
     * @return mixed
     */
    public static function get($class)
    {
        if (! class_exists($class) || ! in_array($class, static::$singletons)) {
            return null;
        }

        if (! isset(static::$container[$class])) {
            static::$container[$class] = new $class;
        }

        return static::$container[$class];
    }

    /**
     * Get Plugin Url
     *
     * @return string
     */
    public static function pluginUrl()
    {
        return plugin_dir_url(__FILE__);
    }

    /**
     * Get Plugin Path
     *
     * @return string
     */
    public static function pluginDir()
    {
        return plugin_dir_path(__FILE__);
    }
}