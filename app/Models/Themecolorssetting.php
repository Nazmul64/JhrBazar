<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Themecolorssetting extends Model
{
    protected $fillable = [
        'primary_color',
        'secondary_color',
        'palette_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Generate a full color palette from a base hex color
     * using lightness steps (50 → 950).
     */
    public static function generatePalette(string $hex): array
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Convert RGB to HSL
        [$h, $s, $l] = self::rgbToHsl($r, $g, $b);

        // Define lightness steps for each shade
        $steps = [
            '50'  => 0.95,
            '100' => 0.90,
            '200' => 0.80,
            '300' => 0.70,
            '400' => 0.60,
            '500' => 0.50,
            '600' => 0.40,
            '700' => 0.32,
            '800' => 0.25,
            '900' => 0.18,
            '950' => 0.12,
        ];

        $palette = [];
        foreach ($steps as $shade => $lightness) {
            [$rr, $gg, $bb] = self::hslToRgb($h, $s, $lightness);
            $palette[$shade] = sprintf('#%02x%02x%02x', $rr, $gg, $bb);
        }

        return $palette;
    }

    /**
     * Derive secondary color (lightest shade = 100) from primary.
     */
    public static function deriveSecondary(string $hex): string
    {
        $palette = self::generatePalette($hex);
        return $palette['100'];
    }

    // ── Helpers ───────────────────────────────────────────────

    private static function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255; $g /= 255; $b /= 255;
        $max = max($r, $g, $b); $min = min($r, $g, $b);
        $l   = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
                case $g: $h = ($b - $r) / $d + 2; break;
                default: $h = ($r - $g) / $d + 4; break;
            }
            $h /= 6;
        }

        return [$h, $s, $l];
    }

    private static function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s == 0) {
            $v = (int) round($l * 255);
            return [$v, $v, $v];
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $hue2rgb = function (float $p, float $q, float $t): float {
            if ($t < 0) $t += 1;
            if ($t > 1) $t -= 1;
            if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
            if ($t < 1/2) return $q;
            if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
            return $p;
        };

        return [
            (int) round($hue2rgb($p, $q, $h + 1/3) * 255),
            (int) round($hue2rgb($p, $q, $h)       * 255),
            (int) round($hue2rgb($p, $q, $h - 1/3) * 255),
        ];
    }
}
