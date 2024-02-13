<?php

if (!function_exists('addon_published_status')) {
    function addon_published_status($module_name)
    {
        $is_published = 0;
        try {
            $full_data = include("Modules/{$module_name}/Addon/info.php");
            $is_published = $full_data['is_published'] == 1 ? 1 : 0;
            return $is_published;
        } catch (\Exception $exception) {
            return 0;
        }
    }
}
