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

if (!function_exists('format_price')) {
    /**
     * Format a price value with currency.
     */
    function format_price($value, $currency = 'BDT') {
        return number_format($value, 2) . " " . $currency;
    }
}
