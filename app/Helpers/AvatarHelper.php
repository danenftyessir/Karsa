<?php

namespace App\Helpers;

/**
 * Helper untuk generate SVG avatar secara inline
 * Menggantikan ui-avatars.com untuk menghindari CORS issues
 */
class AvatarHelper
{
    /**
     * Generate SVG avatar untuk company/user
     *
     * @param string $name Nama company/user
     * @param string $bgColor Background color (hex tanpa #)
     * @param string $textColor Text color (hex tanpa #)
     * @param int $size Size of the avatar
     * @return string SVG string
     */
    public static function generateSvg(
        string $name,
        string $bgColor = 'F59E0B',
        string $textColor = 'ffffff',
        int $size = 200
    ): string {
        // Ambil initial (2 karakter pertama atau 1 jika nama pendek)
        $initial = self::getInitials($name);

        // Pastikan color format tanpa #
        $bgColor = ltrim($bgColor, '#');
        $textColor = ltrim($textColor, '#');

        // Font size proportional to avatar size
        $fontSize = (int)($size * 0.4);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}">
    <rect width="{$size}" height="{$size}" fill="#{$bgColor}"/>
    <text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" font-family="system-ui, -apple-system, sans-serif" font-size="{$fontSize}px" font-weight="600" fill="#{$textColor}">{$initial}</text>
</svg>
SVG;

        return $svg;
    }

    /**
     * Generate data URI SVG avatar
     *
     * @param string $name Nama company/user
     * @param string $bgColor Background color (hex tanpa #)
     * @param string $textColor Text color (hex tanpa #)
     * @param int $size Size of the avatar
     * @return string Data URI string untuk src attribute
     */
    public static function generateDataUri(
        string $name,
        string $bgColor = 'F59E0B',
        string $textColor = 'ffffff',
        int $size = 200
    ): string {
        $svg = self::generateSvg($name, $bgColor, $textColor, $size);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get initials dari nama
     * Ambil 2 karakter pertama untuk initials
     *
     * @param string $name
     * @return string
     */
    protected static function getInitials(string $name): string
    {
        $name = trim($name);

        if (empty($name)) {
            return 'C';
        }

        // Split berdasarkan spasi atau karakter khusus
        $words = preg_split('/[\s\-_\.]+/', $name);

        if (count($words) >= 2) {
            // Ambil huruf pertama dari 2 kata pertama
            return strtoupper(
                mb_substr($words[0], 0, 1) .
                mb_substr($words[1], 0, 1)
            );
        }

        // Jika hanya 1 kata, ambil 2 karakter pertama atau 1 jika pendek
        return strtoupper(mb_substr($name, 0, 2));
    }

    /**
     * Generate random color based on string
     * Untuk consistency warna berdasarkan nama
     *
     * @param string $string
     * @return string Hex color tanpa #
     */
    public static function generateColorFromString(string $string): string
    {
        // Hash string untuk dapat nilai numeric
        $hash = md5($string);

        // Ambil 6 karakter pertama sebagai color
        $color = substr($hash, 0, 6);

        // Pastikan color cukup terang untuk text putih
        // Convert ke HSL, adjust lightness, convert back
        return self::adjustBrightness($color);
    }

    /**
     * Adjust brightness dari hex color
     * Pastikan color cukup saturated untuk avatar
     *
     * @param string $hexColor
     * @return string
     */
    protected static function adjustBrightness(string $hexColor): string
    {
        // Pilih dari palette predefined yang bagus untuk avatar
        $palette = [
            'F59E0B', // amber-500
            '3B82F6', // blue-500
            '8B5CF6', // violet-500
            'EC4899', // pink-500
            '10B981', // emerald-500
            '6366F1', // indigo-500
            'F97316', // orange-500
            '14B8A6', // teal-500
            'A855F7', // purple-500
            '06B6D4', // cyan-500
        ];

        // Hash untuk dapat consistent index
        $index = hexdec(substr($hexColor, 0, 2)) % count($palette);

        return $palette[$index];
    }
}
