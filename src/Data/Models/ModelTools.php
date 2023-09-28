<?php

namespace TestBlocks\Data\Models;

use TestBlocks\Core;
use TestBlocks\ValueObjects\Tools;

class ModelTools
{
    /**
     * Post meta name
     * @var string
     */
    private const TOOLS_NAME = 'tools_name';

    /**
     * Post type id
     * @var string
     */
    private const POST_TYPE_TOOLS_ID = Core::POST_TYPE_TOOLS_ID;

    /**
     * ModelTools constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get CPT casino data custom fields
     * @param int $postId
     *
     * @return Tools
     */
    public function get($postId): Tools
    {
        $cache_key = __CLASS__ . '_tools_' . $postId;
        $data = wp_cache_get($cache_key);

        if (! $data) {
            $data = [
                self::TOOLS_NAME => get_post_meta($postId, self::TOOLS_NAME, true)
            ];
        }

        wp_cache_add($cache_key, $data);

        return $this->setVO($postId, $data);
    }
    
    /**
     * Get all tools pages
     * @param bool $updateCache
     *
     * @return false|int[]|mixed|\WP_Post[]
     */
    public function getAll($updateCache = true)
    {
        $cache_key = __CLASS__ . '_tools';
        $data = wp_cache_get($cache_key);

        if (! $data) {
            $data = get_posts(
                [
                    'posts_per_page' => -1,
                    'meta_key' => self::TOOLS_NAME,
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                    'post_type' => static::POST_TYPE_TOOLS_ID,
                    'nopaging' => true,
                    'cache_results' => $updateCache,
                    'update_post_meta_cache' => $updateCache
                ]
            );
        }

        wp_cache_add($cache_key, $data);

        return $data;
    }

    /**
     * Update CPT cusotm fields
     * @param Tools $data
     */
    public function update(Tools $data)
    {
        $post_id = $data->getPostId();

        update_post_meta(
            $post_id,
            static::TOOLS_NAME,
            sanitize_text_field($data->getToolsName())
        );
    }

    /**
     * Set VO
     */
    private function setVO($postId, $data): Tools
    {
        return new Tools(
            $postId,
            isset($data[static::TOOLS_NAME]) ? $data[static::TOOLS_NAME] : ''
        );
    }
}
