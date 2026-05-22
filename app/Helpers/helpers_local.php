<?php
/**
 * Helper functions for the JhrBazar application.
 * Add any global helper functions here.
 */

if (!function_exists('format_price')) {
    /**
     * Format a price value with currency.
     */
    function format_price($value, $currency = 'BDT') {
        return number_format($value, 2) . " " . $currency;
    }
}

// Add more helper functions as needed.
?>
