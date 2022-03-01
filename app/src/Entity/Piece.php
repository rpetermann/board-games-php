<?php

namespace App\Entity;

use App\Repository\PieceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PieceRepository::class)
 */
class Piece extends AbstractEntity
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
    protected $x;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"game"})
     * @Assert\NotNull
     */
    protected $y;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"game"})
     * @Assert\NotNull
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="pieces")
     * @Assert\Valid
     */
    protected $player;

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
     * getX
     *
     * @return integer|null
     */
    public function getX(): ?int
    {
        return $this->x;
    }

    /**
     * setX
     *
     * @param integer $x
     * @return self
     */
    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    /**
     * getY
     *
     * @return integer|null
     */
    public function getY(): ?int
    {
        return $this->y;
    }

    /**
     * setY
     *
     * @param integer $y
     * @return self
     */
    public function setY(int $y): self
    {
        $this->y = $y;

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
     * getPlayer
     *
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    /**
     * setPlayer
     *
     * @param Player|null $player
     * @return self
     */
    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    /**
     * getGame
     *
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->getPlayer()->getGame();
    }

    /**
     * getBoard
     *
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->getGame()->getBoard();
    }
}
