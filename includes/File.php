<?php

namespace SWI\Includes;

require_once ABSPATH.'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH.'wp-admin/includes/class-wp-filesystem-direct.php';

use \WP_Filesystem_Direct;

class File
{
    public static $instance;

    public static function get_instance(): WP_Filesystem_Direct
    {
        return self::$instance ?: self::$instance = new WP_Filesystem_Direct([]);
    }

    public static function delete(string $file_name): bool
    {
        $wp_filesystem = self::get_instance();
        return ($wp_filesystem->exists($file_name)) ?
            $wp_filesystem->delete($file_name) : false;
    }

    public static function get_filepath(string $file_name): string
    {
        return sprintf(
            '%s%s%s',
            wp_get_upload_dir()['basedir'],
            DIRECTORY_SEPARATOR,
            $file_name
        );
    }

    public static function get_filepath_sized(string $file_name, array $size): string
    {
        $file_path_arr = explode(DIRECTORY_SEPARATOR, $file_name);
        return sprintf(
            '%s%s%s%s%s%s%s',
            wp_get_upload_dir()['basedir'],
            DIRECTORY_SEPARATOR,
            $file_path_arr[0],
            DIRECTORY_SEPARATOR,
            $file_path_arr[1],
            DIRECTORY_SEPARATOR,
            $size['file']
        );
    }

    public static function exists(string $file_name): bool
    {
        $wp_filesystem = self::get_instance();
        return $wp_filesystem->exists($file_name);
    }

    public static function mkdir(string $file_name): bool
    {
        $wp_filesystem = self::get_instance();
        return $wp_filesystem->mkdir($file_name, 0755);
    }

    public static function copy(string $source, string $destination): bool
    {
        $wp_filesystem = self::get_instance();
        return $wp_filesystem->copy($source, $destination);
    }

    public static function deletes(string $file_name): bool
    {
        $wp_filesystem = self::get_instance();
        return $wp_filesystem->delete($file_name);
    }
}