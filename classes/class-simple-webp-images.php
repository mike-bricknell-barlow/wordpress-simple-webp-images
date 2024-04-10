<?php

use SWI\Includes\Jpeg;
use SWI\Includes\Png;
use SWI\Includes\File;

class Simple_Webp_Images
{
    private $plugin_url;

    public function __construct()
    {
        add_filter('wp_generate_attachment_metadata', [$this, 'generate_webp_on_resize'], 10, 2);

        add_action('init', [$this, 'set_plugin_url']);
        add_action('delete_attachment', [$this, 'delete_webp_on_media_deletion']);
        add_action('wp_ajax_convert_single_attachment', [$this, 'convert_single_attachment']);
    }

    public function set_plugin_url()
    {
        $this->plugin_url = SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL;
    }

    public function get_plugin_url(): string
    {
        return $this->plugin_url;
    }

    public function generate_webp_on_resize(array $metadata): array
    {
        foreach($metadata['sizes'] as $size) {
            $this->generate_webp(
                File::get_filepath_sized($metadata['file'], $size),
                $size['mime-type']
            );
        }

        $this->generate_webp(
            File::get_filepath($metadata['file']),
            $metadata['sizes'][array_key_first($metadata['sizes'])]['mime-type']
        );
        
        return $metadata;
    }

    private function generate_webp(string $filename, string $filetype): bool
    {
        $handle = false;

        switch ($filetype) {
            case 'image/jpeg':
                $handle = new Jpeg();
                break;

            case 'image/png':
                $handle = new Png();
                break;
        }

        if (!$handle) {
            return false;
        }

        return $handle->filename_to_webp($filename);
    }

    public function delete_webp_on_media_deletion(int $post_id): void
    {
        $attachment_meta = get_post_meta($post_id, '_wp_attachment_metadata', true);
        foreach($attachment_meta['sizes'] as $size) {
            File::delete(
                File::get_filepath_sized($attachment_meta['file'], $size)
            );
        }

        File::delete(
            File::get_filepath($attachment_meta['file'].'.webp')
        );
    }

    public function convert_single_attachment(): void
    {
        if (!isset($_POST['attachment_id'])) {
            wp_send_json_error([
                'message' => 'Missing attachment ID.',
            ], 500);
            exit();
        }

        if ($this->convert_single_attachment_by_attachment_id(absint($_POST['attachment_id']))) {
            wp_send_json_success([
                'message' => 'Success',
            ], 200);
            exit();
        }

        wp_send_json_error([
            'message' => 'Conversion failed',
        ], 500);
        exit();
    }

    public function convert_single_attachment_by_attachment_id (int $attachment_id): bool
    {
        $attachment_meta = get_post_meta($attachment_id, '_wp_attachment_metadata', true);

        foreach($attachment_meta['sizes'] as $size) {
            $this->generate_webp(
                File::get_filepath_sized($attachment_meta['file'], $size),
                $size['mime-type']
            );
        }

        return $this->generate_webp(
            File::get_filepath($attachment_meta['file']),
            $attachment_meta['sizes'][array_key_first($attachment_meta['sizes'])]['mime-type']
        );
    }
}