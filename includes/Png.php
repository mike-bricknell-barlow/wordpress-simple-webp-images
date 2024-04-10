<?php

/**
 * @package Simple Webp Images
 * @author Mike Bricknell-Barlow
 *
 * @since 2.0.0
 *
 * Extends Image class
 * Handles png images
 */

namespace SWI\Includes;

use GdImage;

class Png extends Image
{
    public function image_from_filename(string $filename): GdImage
    {
        return imagecreatefrompng($filename);
    }
}