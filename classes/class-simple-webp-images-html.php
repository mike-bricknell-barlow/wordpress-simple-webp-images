<?php

use SWI\Includes\Options;

class Simple_Webp_Images_HTML
{
    function __construct()
    {
        add_action('wp_ajax_output_single_convert_link', [$this, 'output_single_convert_link']);

        if (Options::output_buffering_enabled()) {
            add_filter('final_output', [$this, 'wrap_img_tags_with_picture_element']);
        } else {
            add_filter('the_content', [$this, 'wrap_img_tags_with_picture_element'], 20);
        }
    }

    public function output_single_convert_link(): void
    {
        load_template(
            sprintf(
                '%s%stemplates%spartial-single-convert-button.php',
                SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR
            ),
            false,
            []
        );
        exit();
    }

    private function is_HTML(string $string): bool
    {
        if ($string != strip_tags($string)) {
            return true;
        } else {
            return false;
        }
    }

    private function is_xml(string $string): bool
    {
        $doc = @simplexml_load_string($string);
        if ($doc) {
            return true; 
        } else {
            return false;
        }
    }

    private function is_valid_string(string $content): bool
    {
        if (current_filter() == 'final_output' && !Options::output_buffering_enabled()) {
            return false;
        }
	    
    	if ($this->is_json($content)) {
            // Ignore JSON payloads, such as those used by Gutenberg in admin
            return false;
        }

        if (wp_doing_ajax()) {
            // Processing HTML generated in ajax requests can break functionality - skip these
            return false;
        }

        if ($this->is_xml($content)) {
            // If string is XML, don't try to process it
            return false;
        }

        if (!$this->is_HTML($content)) {
            // If string is not HTML, don't try to process it
            return false;
        }

        return true;
    }
	
    public function is_json(string $string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function wrap_img_tags_with_picture_element(string $content): string
    {
        if (!$this->is_valid_string($content)) {
            return $content;
        }
        
        preg_match_all(
            '/<img (.*?)>/m',
            $content,
            $imgs
        );

        foreach ($imgs[0] as $img) {
           if (
               !str_contains($img, '.jpg') &&
               !str_contains($img, '.jpeg') &&
               !str_contains($img, '.png')
           ) {
                continue;
           }

           $content = str_replace(
               $img,
               $this->generate_picture_element($img),
               $content
           );
        }
	
        return $content;
    }

    private function generate_source_elements (string $img_tag): string
    {
        $srcset = preg_match('/srcset="(.*?)"/', $img_tag, $matches) ? $matches[1] : '';
        return sprintf(
            '<source src="%s.webp" srcset="%s" sizes="%s" />',
            preg_match('/src="(.*?)"/', $img_tag, $matches) ? $matches[1] : '',
            str_replace([
                '.jpg',
                '.jpeg',
                '.png',
            ], [
                '.jpg.webp',
                '.jpeg.webp',
                '.png.webp'
            ], $srcset),
            preg_match('/sizes="(.*?)"/', $img_tag, $matches) ? $matches[1] : ''
        );
    }

    private function is_excluded_from_lazy_loading(string $classes): bool
    {
        $excluded_classes = Options::get_lazy_loading_excluded_classes();

        if(!$excluded_classes) {
            return false;
        }

        $excluded_classes_array = array_filter(array_map( 'trim', explode(',', $excluded_classes)));
        $img_classes_array = array_filter(array_map( 'trim', explode(' ', $classes)));

        foreach ($img_classes_array as $img_class) {
            if (in_array($img_class, $excluded_classes_array)) {
                return true;
            }
        }
        
        return false;
    }

    private function generate_picture_element (string $img_tag): string
    {
        $source = $this->generate_source_elements($img_tag);
        
        $new_img_tag = sprintf(
            '<picture>
                %s
                %s
            </picture>',
            $source,
            $img_tag
        );

        $classes = preg_match('/class="(.*?)"/', $img_tag, $matches) ? $matches[1] : '';
        if (Options::lazy_loading_enabled() && !$this->is_excluded_from_lazy_loading($classes)) {
            $new_img_tag = str_replace('src="', 'loading="lazy" src="', $new_img_tag);
        }

        return $new_img_tag;
    }
}

