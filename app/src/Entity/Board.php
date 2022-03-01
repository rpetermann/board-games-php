<?php

namespace App\Entity;

use App\Repository\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BoardRepository::class)
 */
class Board extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     * @Groups({"game"})
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"game"})
     * @Assert\NotNull
     */
    protected $rows;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"game"})
     * @Assert\NotNull
     */
    protected $columns;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"game"})
     * @Assert\NotNull
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="board")
     * @Assert\Valid
     */
    protected $games;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

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
     * getRows
     *
     * @return integer|null
     */
    public function getRows(): ?int
    {
        return $this->rows;
    }

    /**
     * setRows
     *
     * @param integer $rows
     * @return self
     */
    public function setRows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * getColumns
     *
     * @return integer|null
     */
    public function getColumns(): ?int
    {
        return $this->columns;
    }

    /**
     * setColumns
     *
     * @param integer $columns
     * @return self
     */
    public function setColumns(int $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * getType
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * setType
     *
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * getGames
     *
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    /**
     * addGame
     *
     * @param Game $game
     * @return self
     */
    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setBoard($this);
        }

        return $this;
    }

    /**
     * removeGame
     *
     * @param Game $game
     * @return self
     */
    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getBoard() === $this) {
                $game->setBoard(null);
            }
        }

        return $this;
    }
}
