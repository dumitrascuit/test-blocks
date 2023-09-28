<?php

namespace TestBlocks\RestApiEndpoints;

/**
 * Class RestApiEndpointCreator
 */
class RestApiEndpointCreator
{
    /**
     * Register the rest API route
     * @param RestApiEndpointAbstract $restRoute
     */
    public function registerRestRoute(RestApiEndpointAbstract $restRoute)
    {
        $restRoute->register();
    }
}
