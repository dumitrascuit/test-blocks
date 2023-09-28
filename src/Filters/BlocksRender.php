<?php


namespace TestBlocks\Filters;

use TestBlocks\Contracts\ControllerInitHooksInterface;
use TestBlocks\Traits\SingletonTrait;
use WP_Block;


class BlocksRender implements ControllerInitHooksInterface
{
    use SingletonTrait;

    /**
     * Init hooks
     */
    public function initHooks()
    {
        add_filter( 'render_block', [ $this, 'fixEnqueueViewScript' ], 10, 3 );
    }

    /**
     * Fixing connecting viewScript if render_callback function is empty.
     * So, block front javascript is not working if we have php generated block. WTF? https://github.com/gziolo Why?
     *
     * https://github.com/WordPress/wordpress-develop/commit/ad976addb3612a77e5e4e2211283514a2aedbf8a#diff-c569b1ecb11a007a563788a481710b51e0cb21cd1d1885490bf9c91777842f23
     *
     * @param string $block_content The block content about to be appended.
     * @param array $parsed_block
     * @param WP_Block $instance The block instance.
     *
     * @return string Rendered block output.
     */
    public function fixEnqueueViewScript( string $block_content, array $parsed_block, WP_Block $instance ): string
    {
        if ( ! empty( $instance->block_type->view_script ) ) {
            wp_enqueue_script( $instance->block_type->view_script );
        }

        return $block_content;
    }
}
