<?php

namespace App\Entity;

use App\Entity\Traits\CrudDatetimeTrait;
use App\Entity\Traits\StateTrait;
use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Player extends AbstractEntity implements WorkflowableInterface
{
    use StateTrait;
    use CrudDatetimeTrait;

    const STATE_CREATING = 'creating';
    const STATE_WAITING_START = 'waiting_start';
    const STATE_WAITING_PLAY = 'waiting_play';
    const STATE_WAITING_OPPONENT_PLAY = 'waiting_opponent_play';
    const STATE_WINNER = 'winner';
    const STATE_DEFEATED = 'defeated';

    const TRANSITION_WAITING_START = 'send_to_waiting_start';
    const TRANSITION_WAITING_PLAY = 'send_to_waiting_play';
    const TRANSITION_WAITING_OPPONENT_PLAY = 'send_to_waiting_opponent_play';
    const TRANSITION_WINNER = 'send_to_winner';
    const TRANSITION_DEFEATED = 'send_to_defeated';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     * @Groups({"game"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"game"})
     * @Assert\NotNull
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups({"game"})
     * @Assert\NotNull
     */
    protected $sequence = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="player", cascade={"persist"})
     * @Assert\NotNull
     * @Assert\Valid
     */
    protected $game;

    /**
     * @ORM\OneToMany(targetEntity=Piece::class, mappedBy="player", cascade={"persist", "remove"})
     * @Groups({"game"})
     * @Assert\Valid
     */
    protected $pieces;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->state = self::STATE_WAITING_START;
        $this->pieces = new ArrayCollection();
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
     * getName
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * getSequence
     *
     * @return integer|null
     */
    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    /**
     * setSequence
     *
     * @param integer $sequence
     * @return self
     */
    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;

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

    /**
     * isFirstPlayer
     *
     * @return boolean
     */
    public function isFirstPlayer(): bool
    {
        return 0 === $this->sequence;
    }

    /**
     * getPieces
     *
     * @return Collection<int, Piece>
     */
    public function getPieces(): Collection
    {
        return $this->pieces;
    }

    /**
     * setPieces
     *
     * @param Piece ...$pieces
     * @return self
     */
    public function setPieces(Piece ...$pieces): self
    {
        foreach ($pieces as $piece) {
            $this->addPiece($piece);
        }

        return $this;
    }

    /**
     * addPiece
     *
     * @param Piece $piece
     * @return self
     */
    public function addPiece(Piece $piece): self
    {
        if (!$this->pieces->contains($piece)) {
            $this->pieces[] = $piece;
            $piece->setPlayer($this);
        }

        return $this;
    }

    /**
     * removePiece
     *
     * @param Piece $piece
     * @return self
     */
    public function removePiece(Piece $piece): self
    {
        if ($this->pieces->removeElement($piece)) {
            // set the owning side to null (unless already changed)
            if ($piece->getPlayer() === $this) {
                $piece->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * hasPieces
     *
     * @return boolean
     */
    public function hasPieces(): bool
    {
        return !$this->pieces->isEmpty();
    }

    /**
     * getPieceById
     *
     * @param string $id
     * @return Piece|null
     */
    public function getPieceById(string $id): ?Piece
    {
        foreach ($this->getPieces() as $piece) {
            if ($id !== $piece->getId()) {
                continue;
            }

            return $piece;
        }

        return null;
    }

    /**
     * getPieceByPosition
     *
     * @param int $x
     * @param int $y
     * @return Piece|null
     */
    public function getPieceByPosition(int $x, int $y): ?Piece
    {
        foreach ($this->getPieces() as $piece) {
            if ($x !== $piece->getX() || $y !== $piece->getY()) {
                continue;
            }

            return $piece;
        }

        return null;
    }

    /**
     * isTurn
     *
     * @return boolean
     */
    public function isTurn(): bool
    {
        return self::STATE_WAITING_PLAY === $this->getState();
    }

    /**
     * countPieces
     *
     * @return integer
     */
    public function countPieces(): int
    {
        return $this->getPieces()->count();
    }
}
