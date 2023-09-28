<?php

namespace TestBlocks\Metaboxes;

use TestBlocks\Contracts\ControllerInitHooksInterface;
use TestBlocks\Core;
use TestBlocks\Data\Models\ModelTools;
use TestBlocks\Helpers\CustomFieldsGenerator;
use TestBlocks\Helpers\LoggerHelper;
use TestBlocks\Traits\SingletonTrait;
use TestBlocks\ValueObjects\Tools;

/**
 * Class MetaboxTools adds a custom metabox for the CPT
 * @package TestBlocks\PostMeta
 */
class MetaboxTools implements ControllerInitHooksInterface
{
    use SingletonTrait;

    /**
     * Post meta name field ID
     * @var string
     */
    private const POST_META_TOOLS_NAME_ID = 'ra_tools_name';

    private const POST_TYPE_TOOLS_ID = Core::POST_TYPE_TOOLS_ID;

    private LoggerHelper $logger;
    private ModelTools $model;
    private CustomFieldsGenerator $fieldsGenerator;

    /**
     * PostMetaCasinoData constructor
     */
    private function __construct()
    {
        $this->logger = new LoggerHelper();
        $this->model = new ModelTools();
        $this->fieldsGenerator = CustomFieldsGenerator::getInstance();
    }

    /**
     * Init hooks
     */
    public function initHooks()
    {
        add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
        add_action(
            'save_post_' . static::POST_TYPE_TOOLS_ID,
            [$this, 'savePost'],
            20,
            3
        );
    }

    /**
     * Add custom meta boxes to the CPT
     */
    public function addMetaBoxes()
    {
        add_meta_box(
            'ra_tools_meta_box',
            'Tools',
            [$this, 'metaBoxCallback'],
            static::POST_TYPE_TOOLS_ID,
            'side'
        );
    }

    /**
     * Output HTML for fields within metabox
     * @param object $post
     */
    public function metaBoxCallback(object $post)
    {
        $tools = $this->model->get($post->ID);

        // Name text input
        echo $this->fieldsGenerator->input(
            $tools->getToolsName(),
            'Tool Name',
            static::POST_META_TOOLS_NAME_ID
        );
    }

    /**
     * Save tools post data
     * @param int $postId
     * @param \WP_Post $post
     * @param bool $update
     */
    public function savePost(int $postId, \WP_Post $post, bool $update)
    {
        if ('auto-draft' === $post->post_status) {
            // Do noting
            return;
        }

        try {
            $tools = new Tools(
                $postId,
                isset($_REQUEST[static::POST_META_TOOLS_NAME_ID]) ? $_REQUEST[static::POST_META_TOOLS_NAME_ID] : ''
            );
            
            $this->model->update($tools);
        } catch (\Exception $e) {
            $this->logger->log(
                sprintf(
                    'Failed to save TOOLS VO. Error: %s',
                    $e->getMessage()
                ),
                $e->getCode()
            );
        }
    }
}
