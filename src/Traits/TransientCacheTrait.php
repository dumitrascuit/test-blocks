<?php

namespace TestBlocks\Traits;

/**
 * Class TransientCacheTrait
 * @package TestBlocks\Traits
 */
trait TransientCacheTrait
{

    /**
     * Get cache for the method
     * @param string $key
     *
     * @return mixed
     */
    private function getMethodCache($key)
    {
        return get_transient($key);
    }

    /**
     * Set transient method cache
     * @param string $key
     * @param mixed $data
     * @param int $expires Expiration time in seconds
     */
    private function setMethodCache($key, $data, $expires)
    {
        set_transient(
            $key,
            $data,
            $expires
        );
    }

    /**
     * Return method cache key
     * @return string
     */
    private function getMethodCacheKey($id)
    {
        return sprintf(
            '%s@%s',
            __CLASS__,
            $id
        );
    }
}
