<?php

namespace TestBlocks\Traits;

/**
 * Trait SingletonTrait
 * @package TestBlocks\Traits
 */
trait SingletonTrait
{
    protected static array $instance;

    /**
     * Return Class instance
     */
    final public static function getInstance()
    {
        $called_class = static::class;
        if ( ! isset( static::$instance[ $called_class ] ) ) {
            static::$instance[ $called_class ] = new $called_class();
        }
        return static::$instance[ $called_class ];
    }
}
