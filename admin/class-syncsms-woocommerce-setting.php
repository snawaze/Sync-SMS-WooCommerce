<?php

class Syncsmssms_WooCommerce_Setting implements Syncsmssms_Register_Interface
{
    private $settings_api;

    public function __construct()
    {
        $this->settings_api = new WeDevs_Settings_API;
    }

    public function register()
    {
        add_action('admin_init', array( $this, 'admin_init' ));
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }

    public function admin_init()
    {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();
    }

    public function admin_menu()
    {
        add_options_page('Sync SMS WooCommerce', 'Sync SMS', 'manage_options', 'syncsms-woocoommerce-setting', array(
            $this,
            'plugin_page'
        ));
    }

    public function get_settings_sections()
    {
        $sections = array(
            array(
                'id'    => 'syncsms_setting',
                'title' => __('Main Settings', 'syncsms-woocoommerce')
            ),
            array(
                'id'    => 'admin_setting',
                'title' => __('Admin Settings', 'syncsms-woocoommerce')
            ),
            array(
                'id'    => 'customer_setting',
                'title' => __('Customer Settings', 'syncsms-woocoommerce')
            )
        );

        $sections = apply_filters('syncsms_setting_section', $sections);

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields()
    {
        //WooCommerce Country
        global $woocommerce;
        $countries_obj   = new WC_Countries();
        $countries   = $countries_obj->__get('countries');
        
        $additional_billing_fields       = '';
        $additional_billing_fields_desc  = '';
        $additional_billing_fields_array = $this->get_additional_billing_fields();
        foreach ($additional_billing_fields_array as $field) {
            $additional_billing_fields .= ', [' . $field . ']';
        }
        if ($additional_billing_fields) {
            $additional_billing_fields_desc = '<br />Custom tags: ' . substr($additional_billing_fields, 2);
        }

        $settings_fields = array(
            'syncsms_setting' => array(
                array(
                    'name'  => 'syncsms_woocommerce_api_key',
                    'label' => __('API Key', 'syncsms-woocoommerce'),
                    'desc'  => __('Your Sync SMS API key (<a href="https://syncsms.net/dashboard/tools" target="blank">Create API Key</a>). Please make sure that everything is correct and required permissions are granted: <strong>send_sms</strong>, <strong>wa_send</strong>', 'syncsms-woocoommerce'),
                    'type'  => 'text',
                ),
                array(
                    'name'          => 'syncsms_woocommerce_service',
                    'label'         => __('Sending Service', 'syncsms-woocoommerce'),
                    'class'         => array('chzn-drop'),
                    'desc'          => 'Select the sending service, please make sure that the api key has the following permissions: <strong>send_sms</strong>, <strong>wa_send</strong>',
                    'type'          => 'select',
                    'options'       => [
                        1 => "SMS",
                        2 => "WhatsApp"
                    ]
                ),
                array(
                    'name'  => 'syncsms_woocommerce_whatsapp',
                    'label' => __('WhatsApp Account ID', 'syncsms-woocoommerce'),
                    'desc'  => 'For WhatsApp service only. WhatsApp account ID you want to use for sending.',
                    'type'  => 'text',
                ),
                array(
                    'name'  => 'syncsms_woocommerce_device',
                    'label' => __('Device Unique ID', 'syncsms-woocoommerce'),
                    'desc'  => 'For SMS service only. Linked device unique ID, please only enter this field if you are sending using one of your devices.',
                    'type'  => 'text',
                ),
                array(
                    'name'  => 'syncsms_woocommerce_gateway',
                    'label' => __('Gateway Unique ID', 'syncsms-woocoommerce'),
                    'desc'  => 'For SMS service only. Partner device unique ID or gateway ID, please only enter this field if you are sending using a partner device or third party gateway.',
                    'type'  => 'text',
                ),
                array(
                    'name'          => 'syncsms_woocommerce_sim',
                    'label'         => __('SIM Slot', 'syncsms-woocoommerce'),
                    'class'         => array('chzn-drop'),
                    'desc'          => 'For SMS service only. Select the sim slot you want to use for sending the messages. This is not used for partner devices and third party gateways.',
                    'type'          => 'select',
                    'options'       => [
                        1 => "SIM 1",
                        2 => "SIM 2"
                    ]
                ),
                array(
                    'name'          => 'syncsms_woocommerce_country_code',
                    'label'         => __('Default Country', 'syncsms-woocoommerce'),
                    'class'         => array('chzn-drop'),
                    'desc'          => 'Selected country will be use as default country info for mobile numbers when country info is not provided by the user.',
                    'type'          => 'select',
                    'options'       => $countries
                ),
                array(
                    'name'  => 'export_syncsms_log',
                    'label' => 'Export Log',
                    'desc'  => '<a href="' . admin_url('admin.php?page=syncsms-download-file&file=Syncsms') . '" class="button button-secondary">Export</a><div id="syncsms_sms[keyword-modal]" class="modal"></div>',
                    'type'  => 'html'
                )
            ),
            'admin_setting'     => array(
                array(
                    'name'    => 'syncsms_woocommerce_admin_suborders_send_sms',
                    'label'   => __('Enable Suborders SMS Notifications', 'syncsms-woocoommerce'),
                    'desc'    => ' ' . __('Enable', 'syncsms-woocoommerce'),
                    'type'    => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'name'    => 'syncsms_woocommerce_admin_send_sms_on',
                    'label'   => __('  Send notification on', 'syncsms-woocoommerce'),
                    'desc'    => __('Choose when to send a status notification message to your admin', 'syncsms-woocoommerce'),
                    'type'    => 'multicheck',
                    'default' => array(
                        'on-hold'    => 'on-hold',
                        'processing' => 'processing'
                    ),
                    'options' => array(
                        'pending'    => ' Pending',
                        'on-hold'    => ' On-hold',
                        'processing' => ' Processing',
                        'completed'  => ' Completed',
                        'cancelled'  => ' Cancelled',
                        'refunded'   => ' Refunded',
                        'failed'     => ' Failed'
                    )
                ),
                array(
                    'name'  => 'syncsms_woocommerce_admin_sms_recipients',
                    'label' => __('Mobile Number', 'syncsms-woocoommerce'),
                    'desc'  => __('Mobile number to receive new order SMS notification. To send to multiple receivers, separate each entry with comma such as 0123456789, 0167888945', 'syncsms-woocoommerce'),
                    'type'  => 'text',
                ),
                array(
                    'name'    => 'syncsms_woocommerce_admin_sms_template',
                    'label'   => __('Admin SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="admin" data-attr-target="admin_setting[syncsms_woocommerce_admin_sms_template]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : You have a new order with order ID [order_id] and order amount [order_currency] [order_amount]. The order is now [order_status].', 'syncsms-woocoommerce')
                )
            ),
            'customer_setting'  => array(
                array(
                    'name'    => 'syncsms_woocommerce_suborders_send_sms',
                    'label'   => __('Enable Suborders SMS Notifications', 'syncsms-woocoommerce'),
                    'desc'    => ' ' . __('Enable', 'syncsms-woocoommerce'),
                    'type'    => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'name'    => 'syncsms_woocommerce_send_sms',
                    'label'   => __('  Send notification on', 'syncsms-woocoommerce'),
                    'desc'    => __('Choose when to send a status notification message to your customer', 'syncsms-woocoommerce'),
                    'type'    => 'multicheck',
                    'options' => array(
                        'pending'    => ' Pending',
                        'on-hold'    => ' On-hold',
                        'processing' => ' Processing',
                        'completed'  => ' Completed',
                        'cancelled'  => ' Cancelled',
                        'refunded'   => ' Refunded',
                        'failed'     => ' Failed'
                    )
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_default',
                    'label'   => __('Default Customer SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="default" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_default]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_pending',
                    'label'   => __('Pending SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="pending" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_pending]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_on-hold',
                    'label'   => __('On-hold SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="on_hold" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_on-hold]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_processing',
                    'label'   => __('Processing SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="processing" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_processing]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_completed',
                    'label'   => __('Completed SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="completed" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_completed]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_cancelled',
                    'label'   => __('Cancelled SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="cancelled" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_cancelled]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_refunded',
                    'label'   => __('Refunded SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="refunded" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_refunded]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                ),
                array(
                    'name'    => 'syncsms_woocommerce_sms_template_failed',
                    'label'   => __('Failed SMS Message', 'syncsms-woocoommerce'),
                    'desc'    => 'Customize your SMS with <button type="button" id="syncsms_sms[open-keywords]" data-attr-type="failed" data-attr-target="customer_setting[syncsms_woocommerce_sms_template_failed]" class="button button-secondary">Keywords</button>',
                    'type'    => 'textarea',
                    'rows'    => '8',
                    'cols'    => '500',
                    'css'     => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'syncsms-woocoommerce')
                )
            )
        );

        $settings_fields = apply_filters('syncsms_setting_fields', $settings_fields);

        return $settings_fields;
    }

    public function plugin_page()
    {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        echo '<input type="hidden" value="' . join(",", $this->get_additional_billing_fields()) . '" id="syncsms_new_billing_field" />';

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    public function get_pages()
    {
        $pages         = get_pages();
        $pages_options = array();
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[ $page->ID ] = $page->post_title;
            }
        }

        return $pages_options;
    }

    public function get_additional_billing_fields()
    {
        $default_billing_fields   = array(
            'billing_first_name',
            'billing_last_name',
            'billing_company',
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_state',
            'billing_country',
            'billing_postcode',
            'billing_phone',
            'billing_email'
        );
        $additional_billing_field = array();
        $billing_fields           = array_filter(get_option('wc_fields_billing', array()));
        foreach ($billing_fields as $field_key => $field_info) {
            if (! in_array($field_key, $default_billing_fields) && $field_info['enabled']) {
                array_push($additional_billing_field, $field_key);
            }
        }

        return $additional_billing_field;
    }
}
