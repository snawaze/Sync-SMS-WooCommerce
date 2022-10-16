<?php

class Syncsmssms_Multivendor implements Syncsmssms_Register_Interface
{
    public function register()
    {
        $this->required_files();
        //create notification instance
        $syncsms_notification = new Syncsmssms_Multivendor_Notification('Wordpress-Woocommerce-Multivendor-Extension-' . Syncsmssms_Multivendor_Factory::$activatedPlugin);

        $registerInstance = new Syncsmssms_WooCommerce_Register();
        $registerInstance->add(new Syncsmssms_Multivendor_Hook($syncsms_notification))
                         ->add(new Syncsmssms_Multivendor_Setting())
                         ->load();
    }

    protected function required_files()
    {
        require_once __DIR__ . '/admin/class-syncsms-multivendor-setting.php';
        require_once __DIR__ . '/abstract/abstract-syncsms-multivendor.php';
        require_once __DIR__ . '/contracts/class-syncsms-multivendor-interface.php';
        require_once __DIR__ . '/class-syncsms-multivendor-factory.php';
        require_once __DIR__ . '/class-syncsms-multivendor-hook.php';
        require_once __DIR__ . '/class-syncsms-multivendor-notification.php';
    }
}
