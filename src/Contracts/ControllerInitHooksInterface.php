<?php

namespace TestBlocks\Contracts;

/**
 * Interface ControllerInitHooksInteface used for all controllers that have add some hooks
 * @package TestBlocks\Contracts
 */
interface ControllerInitHooksInterface
{
    /**
     * Init hooks
     */
    public function initHooks();
}
