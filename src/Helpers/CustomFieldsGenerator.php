<?php

namespace TestBlocks\Helpers;

use TestBlocks\Traits\SingletonTrait;

/**
 * Class CustomFieldsGenerator used to generate fields for custom metaboxes
 * @package TestBlocks\Helpers
 */
class CustomFieldsGenerator
{
    use SingletonTrait;

    private function __construct()
    {
    }

    /**
     * Return input field html
     *
     * @param mixed $value Value from DB
     * @param string $label Field label
     * @param string $name Field html name attribute
     *
     * @return string
     */
    public function input($value, $label, $name): string
    {
        return sprintf(
            '<div>
                <label for="%1$s"><strong>%1$s</strong></label>
                <p style="margin: 1em 0;">
                    <input type="text" name="%2$s" id="%2$s" value="%3$s">
                </p>
            </div>',
            strip_tags($label), // %1$s
            esc_attr($name), // %2$s
            sanitize_text_field($value), // %3$s
        );
    }
}



