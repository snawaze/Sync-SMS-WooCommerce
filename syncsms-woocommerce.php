<?php

/*
Plugin Name: Sync SMS
Description: Sync SMS Plugin For WooCommerce - Send WooCommerce Notifications To Customer &amp; Admins. You Can Use WhatsApp, Own Device And Our Gateway Credits.
Version:     2.0
Author:      Sync SMS
Author URI:  Https://syncsms.net
License:     GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: syncsms-woocommerce
*/


if (! defined('WPINC')) {
    die;
}

add_action('plugins_loaded', 'syncsms_woocommerce_init', PHP_INT_MAX);

function syncsms_woocommerce_init()
{
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
    require_once ABSPATH . '/wp-includes/pluggable.php';
    require_once plugin_dir_path(__FILE__) . 'includes/contracts/class-syncsms-register-interface.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-syncsms-helper.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-syncsms-woocommerce-frontend-scripts.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-syncsms-woocommerce-hook.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-syncsms-woocommerce-register.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-syncsms-woocommerce-logger.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-syncsms-woocommerce-notification.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-syncsms-download-log.php';
    require_once plugin_dir_path(__FILE__) . 'includes/multivendor/class-syncsms-multivendor.php';
    require_once plugin_dir_path(__FILE__) . 'admin/class-syncsms-woocommerce-setting.php';
    require_once plugin_dir_path(__FILE__) . 'lib/class.settings-api.php';

    //create notification instance
    $syncsms_notification = new Syncsmssms_WooCommerce_Notification();

    //register hooks and settings
    $registerInstance = new Syncsmssms_WooCommerce_Register();
    $registerInstance->add(new Syncsmssms_WooCommerce_Hook($syncsms_notification))
                     ->add(new Syncsmssms_WooCommerce_Setting())
                     ->add(new Syncsmssms_WooCommerce_Frontend_Scripts())
                     ->add(new Syncsmssms_Multivendor())
                     ->add(new Syncsmssms_Download_log())
                     ->load();
}
