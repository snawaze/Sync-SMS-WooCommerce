<?php

if (! function_exists('syncsms_get_options')) {
    /**
     * @param $option
     * @param $section
     * @param array|string $default
     *
     * @return mixed
     */
    function syncsms_get_options($option, $section, $default = '')
    {
        $options = get_option($section);

        if (isset($options[ $option ])) {
            return $options[ $option ];
        }

        return $default;
    }
}

if (! function_exists('syncsms_add_actions')) {
    /**
     * @param array $hook_actions
     */
    function syncsms_add_actions($hook_actions)
    {
        foreach ($hook_actions as $hook) {
            add_action($hook['hook'], $hook['function_to_be_called']);
        }
    }
}
