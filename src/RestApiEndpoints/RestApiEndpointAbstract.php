<?php

namespace TestBlocks\RestApiEndpoints;

use TestBlocks\Helpers\LoggerHelper;

/**
 * Class RestApiEndpointAbstract
 * @package TestBlocks\RestApiEndpoints
 */
abstract class RestApiEndpointAbstract
{
    protected const NAMESPACE = 'ra/v2';
    protected string $route;
    protected string $method;
    protected LoggerHelper $logger;

    /**
     * RestApiEndpointAbstract constructor.
     */
    public function __construct()
    {
        $this->logger = new LoggerHelper();
    }

    /**
     * Register new rest API route
     */
    public function register()
    {
        register_rest_route(static::NAMESPACE, $this->route, [
            'method' => $this->method,
            'callback' => [$this, 'callback'],
            'permission_callback' => [$this, 'permissionCallback']
        ]);
    }

    /**
     * Callback to return the data
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed
     */
    abstract public function callback(\WP_REST_Request $request);

    /**
     * Return if the user can access the endpoint
     * @return bool
     */
    public function permissionCallback(): bool
    {
        return current_user_can('edit_others_posts');
    }
}
