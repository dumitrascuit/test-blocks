<?php

namespace TestBlocks\Helpers;

use TestBlocks\Traits\SingletonTrait;

/**
 * Class WordpressHelper
 * @package TestBlocks\Helpers
 */
class WordpressHelper
{
    use SingletonTrait;

    /**
     * Return Wordpress version
     *
     * @return string
     */
    public function getWordpressVersion(): string
    {
        global $wp_version;

        return $wp_version;
    }
}
