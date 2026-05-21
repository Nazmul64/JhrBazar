<?php

if (!function_exists('settings')) {
    /**
     * Retrieve the first GeneralSetting model instance.
     *
     * @return \App\Models\GenaralSetting|null
     */
    function settings()
    {
        return \App\Models\GenaralSetting::first();
    }
}
