<?php

namespace SWI\Includes;

class Options
{
    public static function get_quality(): int
    {
        return absint(get_option('simple-webp-images-conversion-quality'));
    }

    public static function lazy_loading_enabled(): bool
    {
        if (apply_filters('simple-webp-images-exclude-from-lazy-loading', false)) {
            return false;
        }

        return get_option('simple-webp-images-lazy-loading') == 'on';
    }

    public static function output_buffering_enabled(): bool
    {
        $excluded_pages = self::get_ob_excluded_pages();

        if (in_array(get_the_id(), $excluded_pages)) {
            return false;
        }

        if (apply_filters('simple-webp-images-exclude-from-output-buffering', false)) {
            return false;
        }

        return get_option('simple-webp-images-output-buffering') == 'on';
    }

    public static function get_ob_excluded_pages(): array
    {
        return get_option('simple-webp-images-excluded-html-ob') ?: [];
    }
}