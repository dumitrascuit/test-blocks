<?php

namespace TestBlocks\Contracts;

/**
 * Interface AdminNoticesInterface
 * @package TestBlocks\Contracts
 */
interface AdminNoticesInterface
{
    /**
     * Add text to display in the notice
     * @param string $text
     */
    public function add($text): void;

    /**
     * Set notice type
     * @return $this
     */
    public function setTypeSuccess(): AdminNoticesInterface;

    /**
     * Set notice type
     * @return $this
     */
    public function setTypeWarning(): AdminNoticesInterface;

    /**
     * Set notice type
     * @return $this
     */
    public function setTypeError(): AdminNoticesInterface;
}
