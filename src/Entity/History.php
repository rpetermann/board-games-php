<?php

namespace App\Entity;

use App\Entity\Traits\CrudDatetimeTrait;
use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class History extends AbstractEntity
{
    use CrudDatetimeTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     * @Groups({"history"})
     */
    protected $id;

    /**
     * @ORM\Column(type="json")
     * @Groups({"history"})
     * @Assert\NotNull
     */
    protected $snapshot = [];

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="history", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    protected $game;

    /**
     * getId
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * setId
     *
     * @param string $id
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * getSnapshot
     *
     * @return array|null
     */
    public function getSnapshot(): ?array
    {
        return $this->snapshot;
    }

    /**
     * setSnapshot
     *
     * @param array $snapshot
     * @return self
     */
    public function setSnapshot(array $snapshot): self
    {
        $this->snapshot = $snapshot;

        return $this;
    }

    /**
     * getGame
     *
     * @return Game|null
     */
    public function getGame(): ?Game
    {
        return $this->game;
    }

    /**
     * setGame
     *
     * @param Game|null $game
     * @return self
     */
    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }
}
