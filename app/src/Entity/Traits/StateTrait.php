<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * StateTrait
 */
trait StateTrait
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    protected $state;

    /**
     * getState
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * setState
     *
     * @param string $state
     * @return self
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
