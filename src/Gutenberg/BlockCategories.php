<?php

namespace TestBlocks\Gutenberg;

use TestBlocks\Contracts\ControllerInitHooksInterface;
use TestBlocks\Core;
use TestBlocks\Helpers\WordpressHelper;
use TestBlocks\Traits\SingletonTrait;

/**
 * Class BlockCategories
 * @package TestBlocks\Gutenberg
 */
class BlockCategories implements ControllerInitHooksInterface
{
    use SingletonTrait;

    /**
     * Gutenberg blocks categories
     * @var string
     */
    private $blocksCategory;

    /**
     * Wordpress version
     * @var string
     */
    private $wordpressVersion;

    private WordpressHelper $wordpressHelper;

    /**
     * BlockCategories constructor.
     */
    private function __construct()
    {
        $this->blocksCategory = Core::BLOCKS_GUTENBERG_CATEGORY;
        $this->wordpressVersion = WordpressHelper::getInstance()->getWordpressVersion();
    }

    /**
     * @inheritDoc
     */
    public function initHooks()
    {
        if (version_compare($this->wordpressVersion, '5.8', '<')) {
            add_filter('block_categories', [$this, 'categories'], 10, 1);
        } else {
            add_filter('block_categories_all', [$this, 'categories'], 10, 1);
        }
    }

    /**
     * Add Madl Gutenberg block category
     * @param array $categories
     * @param \WP_Post $post
     *
     * @see https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#managing-block-categories
     *
     * @return array
     */
    public function categories(array $categories): array
    {
        global $post;

        return array_merge(
            [
                [
                    'slug' => $this->blocksCategory,
                    'title' => 'Madl Crm'
                ],
            ],
            $categories
        );
    }
}
