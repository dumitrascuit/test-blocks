<?php

namespace TestBlocks\Blocks;

use TestBlocks\Traits\BlockFunctionsTrait;
use TestBlocks\Data\Models\ModelTools;

/**
 * Class BlockToolsSingle
 * @package TestBlocks\Blocks
 */
class BlockToolsSingle extends BlocksAbstract
{
    use BlockFunctionsTrait;

    private ModelTools $model; 

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new ModelTools();
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    protected function setTemplateAttributes(array $attributes): array
    {
        $toolsData = [
            'error' => true,
            'errorMessage' => 'Please select a Tool!'
        ];

        if (isset($attributes['toolsId']) && ! empty($attributes['toolsId'])) {
            $toolsData = $this->getToolsData(intval($attributes['toolsId']));
        }

        if ($toolsData['error']) {
            return $toolsData;
        }

        return [
            'error' => false,
            'name' => $toolsData['name'],
            'url' => $toolsData['url'],
            'thumbnail' => $toolsData['thumbnail'],
            'description' => (isset($attributes['toolsDescription']) && $attributes['toolsDescription']) ? $attributes['toolsDescription'] : '',
            'aTag' => $this->isGutenbergEditor() ? 'span' : 'a'
        ];
    }

    /**
     * @param array $attributes
     */
    protected function setTemplate(array $attributes)
    {
        if ($attributes['error']) {
            $this->template = 'common/cantDisplay.html.twig';

            return;
        }

        $this->template = 'toolsSingle/default.html.twig';
    }

    /**
     * Get tools data
     * @param int $id
     * @return array
     */
    private function getToolsData(int $id): array
    {
        $tool = $this->model->get($id);

        return [
            'error' => false,
            'url' => get_permalink($tool->getPostId()),
            'name' => $tool->getToolsName(),
            'thumbnail' => get_the_post_thumbnail($id, 'medium')
        ];
    }
}
