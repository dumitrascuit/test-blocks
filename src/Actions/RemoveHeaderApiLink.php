<?php

namespace TestBlocks\Actions;

use TestBlocks\Traits\SingletonTrait;

class RemoveHeaderApiLink
{
    use SingletonTrait;

    /**
     * Class constructor
     */
    public function __construct()
    {
    }

    /**
     * remove api link in wp_head
     */
    public function initHooks()
    {
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10);
    }
}
