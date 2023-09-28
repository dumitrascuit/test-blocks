<?php

namespace TestBlocks\PostTypes;

use TestBlocks\Contracts\ControllerInitHooksInterface;
use TestBlocks\Core;
use TestBlocks\Traits\SingletonTrait;

/**
 * Class PostTypeTools
 * @package TestBlocks\PostTypes
 */
class PostTypeTools implements ControllerInitHooksInterface
{
    use SingletonTrait;

    private const ID = Core::POST_TYPE_TOOLS_ID;

    /**
     * PostTypeTools constructor
     */
    private function __construct()
    {
    }

    /**
     * Init hooks
     */
    public function initHooks()
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Register tools custom post type
     */
    public function register()
    {
        $args = [
            'label' => 'RA Tools',
            'public' => true,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-bank',
            'supports' => ['title', 'editor', 'excerpt', 'revisions', 'thumbnail', 'page-attributes', 'author'],
            'rewrite' => ['with_front' => false, 'slug' => 'trading-tools'],
            'capability_type' => 'page',
            'hierarchical' => true
        ];

        register_post_type(static::ID, $args);
    }
}
