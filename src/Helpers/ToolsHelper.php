<?php

namespace TestBlocks\Helpers;

use TestBlocks\Data\Models\ModelTools;
use TestBlocks\Exception\ExceptionTools;

class ToolsHelper
{
    private ModelTools $model;
    private LoggerHelper $logger;

    public function __construct()
    {
        $this->model = new ModelTools();
        $this->logger = new LoggerHelper();
    }

     /**
     * Return array of Tools VO
     *
     * @throws ExceptionTools
     * @return array
     */
    public function getAllTools(): array
    {
        $cache_key = __CLASS__ . '_tools_helper';

        $data = wp_cache_get($cache_key);

        if ($data) {
            return $data;
        }
        
        try {
            $toolsCpt = $this->model->getAll();
        } catch (ExceptionTools $e) {
            $this->logger->log(
                $e->getMessage(),
                $e->getCode()
            );
        }

        $tools = [];

        for ($i=0; $i < count($toolsCpt); $i++) {
            $tools[$i] = $this->model->get($toolsCpt[$i]->ID);
        }

        wp_cache_add($cache_key, $tools);

        return $tools;
    }
}
