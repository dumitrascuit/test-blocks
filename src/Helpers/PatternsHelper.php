<?php

namespace TestBlocks\Helpers;

use WP_Post;

/**
 * Class PatternsHelper
 * Helper functions for patterns
 *
 * @package TestBlocks\Patterns
 */
class PatternsHelper
{
    /**
     *
     */
    public static function hasPattern( $patternName, $post = null ): bool
    {

        if ( ! is_string( $post ) ) {
            $wp_post = get_post( $post );
            if ( $wp_post instanceof WP_Post ) {
                $post = $wp_post->post_content;
            }
        }

        $patternClass = str_replace( '/', '-', $patternName );

        $hasPattern = false !== strpos( $post, $patternClass . ' ' );

        if ( ! $hasPattern ) {
            $hasPattern = false !== strpos( $post, $patternClass . '"' );
        }

        return $hasPattern;
    }

    /**
     * Register pattern styles and scripts
     *
     * @param array $metadata Pattern metadata
     *
     * @return array with assets handles provided directly or created through style's registration.
     */
    public static function registerPatternAssets( array $metadata ): array
    {
        if ( empty( $metadata['file'] ) ) {
            return [];
        }

        $result = [];

        if ( ! empty( $metadata['editorScript'] ) ) {
            $result['editorScript'] = register_block_script_handle(
                $metadata,
                'editorScript'
            );
        }

        if ( ! empty( $metadata['script'] ) ) {
            $result['script'] = register_block_script_handle(
                $metadata,
                'script'
            );
        }

        if ( ! empty( $metadata['viewScript'] ) ) {
            $result['viewScript'] = register_block_script_handle(
                $metadata,
                'viewScript'
            );
        }

        if ( ! empty( $metadata['editorStyle'] ) ) {

            $metadata = PatternsHelper::updatePatternsAssetsVersion( $metadata, 'editorStyle' );

            $result['editorStyle'] = register_block_style_handle(
                $metadata,
                'editorStyle'
            );
        }

        if ( ! empty( $metadata['style'] ) ) {

            $metadata = PatternsHelper::updatePatternsAssetsVersion( $metadata, 'style' );

            $result['style'] = register_block_style_handle(
                $metadata,
                'style'
            );
        }

        return $result;
    }

    /**
     * Connect pattern or block registered styles and scripts from plugin and from theme
     *
     * @param array $metadata Block or Pattern metadata
     * @param bool $editor True if editor context
     *
     * @return void
     */
    public static function enqueueBlockAssets( array $metadata, bool $editor = false ): void
    {
        // Assets from plugin
        if ( ! empty( $metadata['editorScript'] ) && $editor ) {
            wp_enqueue_script( $metadata['editorScript'] );
        }
        if ( ! empty( $metadata['script'] ) ) {
            wp_enqueue_script( $metadata['script'] );
        }
        if ( ! empty( $metadata['viewScript'] ) && ! $editor ) {
            wp_enqueue_script( $metadata['viewScript'] );
        }
        if ( ! empty( $metadata['style'] ) ) {
            wp_enqueue_style( $metadata['style'] );
        }
        if ( ! empty( $metadata['editorStyle'] ) && $editor ) {
            wp_enqueue_style( $metadata['editorStyle'] );
        }

        // Assets from theme
        if ( ! empty( $metadata['editorScriptTheme'] ) && $editor ) {
            wp_enqueue_script( $metadata['editorScriptTheme'] );
        }
        if ( ! empty( $metadata['scriptTheme'] ) ) {
            wp_enqueue_script( $metadata['scriptTheme'] );
        }
        if ( ! empty( $metadata['viewScriptTheme'] ) && ! $editor ) {
            wp_enqueue_script( $metadata['viewScriptTheme'] );
        }
        if ( ! empty( $metadata['styleTheme'] ) ) {
            wp_enqueue_style( $metadata['styleTheme'] );
        }
        if ( ! empty( $metadata['editorStyleTheme'] ) && $editor ) {
            wp_enqueue_style( $metadata['editorStyleTheme'] );
        }
    }

    /**
     * Merge assets from plugin and theme
     *
     * @param array $patternAttributes
     * @param array $assets
     * @param array $themeAssets
     *
     * @return array
     */
    public static function getPatternAttributes( array $patternAttributes, array $assets, array $themeAssets ): array
    {
        foreach ( $assets as $assetKey => $asset ) {
            if ( ! empty( $asset ) ) {
                $patternAttributes[ $assetKey ] = $asset;
            }
        }

        foreach ( $themeAssets as $assetKey => $asset ) {
            if ( ! empty( $asset ) ) {
                $patternAttributes[ $assetKey . 'Theme' ] = $asset;
            }
        }

        return $patternAttributes;
    }

    /**
     * Add filemtime() version to patterns style files.
     *
     * @param $metadata
     * @param $fieldName
     *
     * @return array
     */
    public static function updatePatternsAssetsVersion( $metadata, $fieldName ): array
    {
        if ( ! empty( $metadata['version'] ) || empty( $metadata[ $fieldName ] ) ) {
            return $metadata;
        }

        $style_path = remove_block_asset_path_prefix( $metadata[ $fieldName ] );

        $block_dir  = dirname( $metadata['file'] );
        $style_file = realpath( "$block_dir/$style_path" );

        $metadata['version'] = filemtime( $style_file );

        return $metadata;
    }

}
