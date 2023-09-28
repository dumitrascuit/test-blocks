<?php

namespace TestBlocks\Traits;

/**
 * Trait BlockFunctions to use in Block PHP controllers
 * @package TestBlocks\Traits
 */
trait BlockFunctionsTrait
{
    /**
     * Check if the request came from the Gutenberg editor from the Admin
     * @return bool
     */
    private function isGutenbergEditor(): bool
    {
        return defined('REST_REQUEST') && REST_REQUEST;
    }
}
