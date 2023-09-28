<?php

namespace TestBlocks;

use TestBlocks\Traits\SingletonTrait;

/**
 * Class Core
 * @package TestBlocks
 */
class Core
{
    use SingletonTrait;

    /**
     * Plugin slug
     * @var string
     */
    public const PLUGIN_SLUG = 'test-blocks';

    /**
     * Plugin short slug
     * @var string
     */
    public const PLUGIN_SHORT_SLUG = 'test';

    /**
     * Plugin dir
     * @var string
     */
    public const PLUGIN_DIR = TEST_BLOCKS_DIR;

    /**
     * Plugin url
     * @var string
     */
    public const PLUGIN_URL = TEST_BLOCKS_URL;

    /**
     * Plugin debug on/off
     * @var bool
     */
    public const DEBUG_ENABLED = TEST_BLOCKS_DEBUG_ENABLED;

    /**
     * Plugin debug log on/off
     * @var bool
     */
    public const DEBUG_LOG_ENABLED = TEST_BLOCKS_DEBUG_LOG_ENABLED;

    /**
     * Gutenberg blocks category
     * @var string
     */
    public const BLOCKS_GUTENBERG_CATEGORY = 'test-blocks';

    /**
     * Gutenberg block patterns category
     * @var string
     */
    public const BLOCK_PATTERNS_GUTENBERG_CATEGORY = 'test-blocks-patterns';

    /**
     * Post type Tools ID
     * @var string
     */
    public const POST_TYPE_TOOLS_ID = 'test-tools';
}
