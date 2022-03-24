<?php

namespace App\Entity;

/**
 * WorkflowableInterface
 */
interface WorkflowableInterface
{
    /**
     * getState
     *
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * setState
     *
     * @param string $state
     * @return self
     */
    public function setState(string $state): self;
}
