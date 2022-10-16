<?php

class Syncsmssms_WooCommerce_Frontend_Scripts implements Syncsmssms_Register_Interface
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array( $this, 'msmswc_admin_enqueue_scripts' ));
    }

    public function msmswc_admin_enqueue_scripts()
    {
        wp_enqueue_script('admin-syncsms-scripts', plugins_url('js/admin.js?v=1', __DIR__), array( 'jquery' ), '1.1.5', true);
        wp_enqueue_style('admin-syncsms-css', 'https://syncsms.net/templates/_plugins/woocommerce/css/jquery.modal.min.css', array(), '0.9.1');
        wp_enqueue_script('jquery_syncsms_modal', 'https://syncsms.net/templates/_plugins/woocommerce/js/jquery.modal.min.js', array( 'jquery' ), '0.9.1', true);
    }
}
