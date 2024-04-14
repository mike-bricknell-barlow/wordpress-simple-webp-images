<?php

/**
 * @package Simple Webp Images
 * @author Mike Bricknell-Barlow
 *
 * @since 2.0.0
 *
 * Abstract class for generating webp images from image types
 * Subclasses should be provided for each image type (file extension)
 */

namespace SWI\Includes;

use GdImage;

abstract class Image
{
    public abstract function image_from_filename(string $filename): GdImage;

    public function image_to_webp(GdImage $image, string $filename): bool
    {
        $quality = new Quality();
        imagepalettetotruecolor($image);
        return imagewebp($image, $filename . '.webp', $quality->get_quality());
    }

    public function filename_to_webp(string $filename): bool
    {
        $image = $this->image_from_filename($filename);
        return $this->image_to_webp($image, $filename);
    }
}