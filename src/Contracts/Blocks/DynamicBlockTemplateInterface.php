<?php

namespace TestBlocks\Contracts\Blocks;

/**
 * Interface DynamicBlockTemplateInterface for the PHP block templates
 * @package TestBlocks\Contracts\Blocks
 */
interface DynamicBlockTemplateInterface
{

    /**
     * Register the dynamic block.
     *
     * @param string $blockRelativePath
     *
     * @since 1.0.0
     */
    public function registerDynamicBlock( string $blockRelativePath );

    /**
     * Register the static block.
     *
     * @param string $blockRelativePath
     *
     * @since 1.0.0
     */
    public function registerStaticBlock( string $blockRelativePath );

    /**
     * Server rendering for block template
     *
     * @param array
     * @param string $renderCallbackContent
     * @return string
     */
    public function renderDynamicBlock(array $attributes, string $renderCallbackContent): string;
}
