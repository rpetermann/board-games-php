<?php

namespace App\Entity;

/**
 * WorkflowableInterface
 */
interface WorkflowableInterface
{
    public function getState(): ?string;

    public function setState(string $state): self;
}
