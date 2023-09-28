<?php

namespace TestBlocks\RestApiEndpoints;

use TestBlocks\Helpers\ToolsHelper;
use TestBlocks\Traits\TransientCacheTrait;

/**
 * Class RestApiEndpointTools
 * @package TestBlocks\RestApiEndpoints
 */
class RestApiEndpointTools extends RestApiEndpointAbstract
{
    use TransientCacheTrait;

    private const TRANSIENT_CACHE_TIME = MINUTE_IN_SECONDS;
    protected string $route = '/tools';
    protected string $method = 'GET';

    private ToolsHelper $toolsHelper;

    /**
     * RestApiEndpointTools constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->toolsHelper = new ToolsHelper();
    }

    /**
     * Callback to return the data
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function callback(\WP_REST_Request $request)
    {
        return rest_ensure_response($this->getTools());
    }

    /**
     * @return array
     */
    private function getTools(): array
    {
        $cacheKey = $this->getMethodCacheKey('ToolsAllIDs');
        $tools = $this->getMethodCache($cacheKey);

        if ($tools) {
            return $tools;
        }

        $tools = [];
        $toolsData = $this->toolsHelper->getAllTools();

        for ($i=0; $i < count($toolsData); $i++) {
            $tools[$i] = [
                'toolsId' => $toolsData[$i]->getPostId(),
                'toolsName' => $toolsData[$i]->getToolsName()
            ];
        }         

        $this->setMethodCache(
            $cacheKey,
            $tools,
            static::TRANSIENT_CACHE_TIME
        );

        return $tools;
    }
}
