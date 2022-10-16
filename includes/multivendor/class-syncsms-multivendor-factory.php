<?php

class Syncsmssms_Multivendor_Factory
{
    /* @var Syncsmssms_WooCoommerce_Logger $logger */
    public $log;
    public static $activatedPlugin = '';

    protected $manager_dir = 'managers';
    protected $mapper = [
        'product_vendors'  => [
            'file'  => 'class-syncsmsapi-multivendor-woocommerce-product-vendors-manager',
            'class' => 'Syncsmsapi_Multivendor_WooCommerce_Product_Vendors_Manager'
        ],
        'dokan'            => [
            'file'  => 'class-syncsmsapi-multivendor-dokan-manager',
            'class' => 'Syncsmsapi_Multivendor_Dokan_Manager'
        ],
        'wc_vendors'       => [
            'file'  => 'class-syncsmsapi-multivendor-wc-vendors-manager',
            'class' => 'Syncsmsapi_Multivendor_Wc_Vendors_Manager'
        ],
        'wc_marketplace'   => [
            'file'  => 'class-syncsmsapi-multivendor-wc-marketplace-manager',
            'class' => 'Syncsmsapi_Multivendor_WC_Marketplace_Manager'
        ],
        'wcfm_marketplace' => [
            'file'  => 'class-syncsmsapi-multivendor-wcfm-marketplace-manager',
            'class' => 'Syncsmsapi_Multivendor_WCFM_Marketplace_Manager'
        ],
        'yith'             => [
            'file'  => 'class-syncsmsapi-multivendor-yith-manager',
            'class' => 'Syncsmsapi_Multivendor_Yith_Manager'
        ]
    ];

    private function __construct(Syncsmssms_WooCoommerce_Logger $log = null)
    {
        if ($log === null) {
            $log = new Syncsmssms_WooCoommerce_Logger();
        }

        $this->log = $log;
    }

    /**
     * @return Abstract_Syncsmssms_Multivendor|bool
     */
    public static function make()
    {
        $factory = new self();

        return $factory->getMultivendorManager();
    }

    protected function getMultivendorManager()
    {
        $selected_setting = syncsms_get_options('syncsms_multivendor_selected_plugin', 'multivendor_setting', 'auto');

        if ($selected_setting === 'auto') {
            //attempt to detect plugin
            $is_plugin_detected = $this->detectPlugin();
            if (! $is_plugin_detected) {
                return false;
            }
        } else {
            self::$activatedPlugin = $selected_setting;
        }

        //required the file
        require_once __DIR__ . '/' . $this->manager_dir . '/' . $this->mapper[ self::$activatedPlugin ]['file'] . '.php';

        return new $this->mapper[self::$activatedPlugin]['class'];
    }

    protected function detectPlugin()
    {
        if (is_plugin_active('woocommerce-product-vendors/woocommerce-product-vendors.php')) {
            self::$activatedPlugin = 'product_vendors';

            return true;
        }

        if (is_plugin_active('dokan-lite/dokan.php') || is_plugin_active('dokan-pro/dokan.php')) {
            self::$activatedPlugin = 'dokan';

            return true;
        }

        if (is_plugin_active('wc-vendors/class-wc-vendors.php')) {
            self::$activatedPlugin = 'wc_vendors';

            return true;
        }

        if (is_plugin_active('dc-woocommerce-multi-vendor/dc_product_vendor.php')) {
            self::$activatedPlugin = 'wc_marketplace';

            return true;
        }

        if (is_plugin_active('wc-multivendor-marketplace/wc-multivendor-marketplace.php') && is_plugin_active('wc-frontend-manager/wc_frontend_manager.php')) {
            self::$activatedPlugin = 'wcfm_marketplace';

            return true;
        }

        if (is_plugin_active('yith-woocommerce-product-vendors/init.php')) {
            self::$activatedPlugin = 'yith';

            return true;
        }

        return false;
    }
}
