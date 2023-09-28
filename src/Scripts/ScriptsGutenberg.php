<?php

namespace TestBlocks\Scripts;

use TestBlocks\Contracts\ControllerInitHooksInterface;
use TestBlocks\Core;
use TestBlocks\Traits\SingletonTrait;
use TestBlocks\Helpers\PatternsHelper;
use WP_Block_Type_Registry;
use WP_Block_Pattern_Categories_Registry;
use WP_Block_Patterns_Registry;

/**
 * Class ScriptsGutenberg
 * @package TestBlocks\Scripts
 */
class ScriptsGutenberg implements ControllerInitHooksInterface
{
    use SingletonTrait;

    private const PLUGIN_URL = Core::PLUGIN_URL;
    private const PLUGIN_DIR = Core::PLUGIN_DIR;

    /**
     * @inheritDoc
     */
    public function initHooks()
    {
        add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorAssets']);
        add_action('enqueue_block_assets', [$this, 'enqueueAssets']);
        add_action('enqueue_block_assets', [$this, 'enqueueFrontendAssets']);

        // Add filemtime() version to blocks style files
        add_filter( 'block_type_metadata', [$this, 'updateBlocksAssetsVersion'], 99 );

        if ( ! is_admin()) {
            // Force enqueue to the head used in content blocks styles
            add_action( 'enqueue_block_assets', [ $this, 'enqueueUsedBlocksStyles' ] );

            add_action( 'enqueue_block_assets', [ $this, 'enqueuePatternsAssets' ], 30 );
        }

        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueueBlockEditorPatternsAssets' ] );
    }

    /**
     * Enqueue block editor only JavaScript and CSS.
     */
    public function enqueueBlockEditorAssets()
    {
        // Make paths variables, so we don't write em twice ;)
        $jsPath = 'assets/dist/js/editor.blocks.js';
        $stylePath = 'assets/dist/css/editor-common.style.css';

        // Register the bundled block JS file
        wp_register_script(
            'madl-crm-editor',
            self::PLUGIN_URL . $jsPath,
            ['wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor'],
            filemtime(self::PLUGIN_DIR . $jsPath),
            true
        );

        // Enqueue the bundled block JS file
        wp_enqueue_script('madl-crm-editor');

        // Enqueue optional editor only styles
        wp_enqueue_style(
            'madl-crm-common-editor',
            self::PLUGIN_URL . $stylePath,
            ['madl-crm-common-styles'],
            filemtime(self::PLUGIN_DIR . $stylePath)
        );
    }

    /**
     * Enqueue front end and editor JavaScript and CSS assets.
     */
    public function enqueueAssets()
    {
        $stylePath = 'assets/dist/css/blocks-common.style.css';

        wp_enqueue_style(
            'madl-crm-common-styles',
            self::PLUGIN_URL . $stylePath,
            null,
            filemtime(self::PLUGIN_DIR . $stylePath)
        );

        // Need to add style path for inlining function Bwt-Addon\Frontend\InlineStylesHelper\maybeInlineStyles,
        // for performance optimization
        wp_style_add_data( 'madl-crm-common-styles', 'path', self::PLUGIN_DIR . $stylePath );
    }

    /**
     * Enqueue frontend JavaScript and CSS assets.
     */
    public function enqueueFrontendAssets()
    {
        // If in the backend, bail out.
        if (is_admin()) {
            return;
        }

        $jsPath = 'assets/dist/js/blocks-frontend-common.script.js';
        $handle = 'madl-crm-frontend-common';

        wp_enqueue_script(
            $handle,
            self::PLUGIN_URL . $jsPath,
            [],
            filemtime(self::PLUGIN_DIR . $jsPath),
            true
        );

        wp_localize_script(
            $handle,
            'madlFrontendData',
            [
                'restApiUrl' => get_rest_url()
            ]
        );
    }

    /**
     * Add filemtime() version to blocks style files.
     *
     * @param $metadata
     *
     * @return array
     */
    public function updateBlocksAssetsVersion( $metadata ): array
    {
        $is_core_block = isset( $metadata['name'] ) && 0 === strpos( $metadata['name'], 'core/' );

        if ( $is_core_block || ! empty( $metadata['version'] ) || empty( $metadata['style'] ) ) {
            return $metadata;
        }

        $style_path = remove_block_asset_path_prefix( $metadata['style'] );

        $block_dir  = dirname( $metadata['file'] );
        $style_file = realpath( "$block_dir/$style_path" );

        $metadata['version'] = filemtime( $style_file );

        return $metadata;
    }

    /**
     * In classic themes (not block themes) if the `should_load_separate_core_block_assets` is true - blocks css files loads in the footer
     * So we need to force enqueue only used blocks css files in the head
     */
    public function enqueueUsedBlocksStyles(): void
    {
        global $post;

        if ( empty( $post->ID ) ) {
            return;
        }

        $registeredBlocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

        foreach ( $registeredBlocks as $block ) {
            if ( has_block( $block->name ) ) {
                if ( ! empty( $block->style ) ) {
                    wp_enqueue_style( $block->style );
                }
            }
        }

    }

    /**
     * enqueue only used patterns css files in the head
     * take all registered patterns and check if they are used in the current post
     */
    public function enqueuePatternsAssets()
    {
        global $post;

        if ( empty( $post->ID ) ) {
            return;
        }

        $registeredCategories = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered();
        foreach ( $registeredCategories as $category ) {
            if ( empty( $category['class'] ) || ! PatternsHelper::hasPattern( $category['class'] ) ) {
                continue;
            }
            PatternsHelper::enqueueBlockAssets( $category );
        }

        $registeredPatterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
        foreach ( $registeredPatterns as $pattern ) {
            if ( empty( $pattern['class'] ) || ! PatternsHelper::hasPattern( $pattern['class'] ) ) {
                continue;
            }
            PatternsHelper::enqueueBlockAssets( $pattern );
        }
    }

    /**
     * Enqueue editor JavaScript and CSS assets for all patterns.
     */
    public function enqueueBlockEditorPatternsAssets()
    {
        $registeredCategories = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered();
        foreach ( $registeredCategories as $category ) {
            PatternsHelper::enqueueBlockAssets( $category, true );
        }

        $registeredPatterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
        foreach ( $registeredPatterns as $pattern ) {
            PatternsHelper::enqueueBlockAssets( $pattern, true );
        }
    }
}
