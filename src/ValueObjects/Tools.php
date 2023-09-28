<?php

namespace TestBlocks\ValueObjects;

/**
 * Class Tools represents a single CPT tools custom fields
 * @package TestBlocks\ValueObjects
 */
class Tools {

    private int $postId;
    private string $toolsName;

    public function __construct($postId, $toolsName)
    {
        $this->postId = $postId;
        $this->toolsName = $toolsName;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getToolsName(): string
    {
        return $this->toolsName;
    }
}
