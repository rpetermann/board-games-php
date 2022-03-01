<?php

namespace App\Entity;

use App\Entity\Traits\CrudDatetimeTrait;
use App\Entity\Traits\StateTrait;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Game extends AbstractEntity implements WorkflowableInterface
{
    use StateTrait;
    use CrudDatetimeTrait;

    const STATE_CREATING = 'creating';
    const STATE_WAITING_PLAYERS = 'waiting_players';
    const STATE_WAITING_START = 'waiting_start';
    const STATE_PLAYING = 'playing';
    const STATE_FINISHED = 'finished';

    const TRANSITION_WAITING_PLAYERS = 'send_to_waiting_players';
    const TRANSITION_WAITING_START = 'send_to_waiting_start';
    const TRANSITION_PLAYING = 'send_to_playing';
    const TRANSITION_FINISHED = 'send_to_finished';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     * @Groups({"game"})
     */
    protected $id;

    /**
     * @ORM\Column(type="uuid")
     * @Groups({"token"})
     * @Assert\Uuid
     */
    protected $accessToken;

    /**
     * @ORM\OneToMany(targetEntity=Player::class, mappedBy="game", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sequence" = "ASC"})
     * @Groups({"game"})
     * @Assert\NotNull
     * @Assert\Valid
     */
    protected $player;

    /**
     * @ORM\OneToMany(targetEntity=History::class, mappedBy="game", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     * @Assert\Valid
     */
    protected $history;

    /**
     * @ORM\ManyToOne(targetEntity=Board::class, inversedBy="games", cascade={"persist", "remove"})
     * @Groups({"game"})
     * @Assert\Valid
     */
    protected $board;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->state = self::STATE_WAITING_PLAYERS; 
        $this->player = new ArrayCollection();
        $this->history = new ArrayCollection();
        $this->accessToken = Uuid::v4();
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
     * getAccessToken
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * getPlayer
     *
     * @return Collection<int, Player>
     */
    public function getPlayer(): Collection
    {
        return $this->player;
    }

    /**
     * addPlayer
     *
     * @param Player $player
     * @return self
     */
    public function addPlayer(Player $player): self
    {
        if (!$this->player->contains($player)) {
            $this->player[] = $player;
            $player->setGame($this);
        }

        return $this;
    }

    /**
     * removePlayer
     *
     * @param Player $player
     * @return self
     */
    public function removePlayer(Player $player): self
    {
        if ($this->player->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    /**
     * countPlayers
     *
     * @return integer
     */
    public function countPlayers(): int
    {
        return $this->player->count();
    }

    /**
     * getPlayerById
     *
     * @param string $id
     * @return Player|null
     */
    public function getPlayerById(string $id): ?Player
    {
        foreach ($this->getPlayer() as $player) {
            if ($id !== $player->getId()) {
                continue;
            }

            return $player;
        }

        return null;
    }

    /**
     * getOpponents
     *
     * @param string $currentPlayerId
     * @return Collection<i, Player>
     */
    public function getOpponents(string $currentPlayerId): Collection
    {
        $opponents = new ArrayCollection();
        foreach ($this->getPlayer() as $player) {
            if ($currentPlayerId === $player->getId()) {
                continue;
            }

            $opponents[] = $player;
        }

        return $opponents;
    }

    /**
     * getOpponentPieceByPosition
     *
     * @param string $currentPlayerId
     * @param int    $x
     * @param int    $y
     * @return Piece|null
     */
    public function getOpponentPieceByPosition(string $currentPlayerId, int $x, int $y): ?Piece
    {
        foreach ($this->getOpponents($currentPlayerId) as $player) {
            return $player->getPieceByPosition($x, $y);
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
        $piece = null;
        foreach ($this->getPlayer() as $player) {
            $piece = $player->getPieceByPosition($x, $y);

            if (!empty($piece)) {
                return $piece;
            }
        }

        return $piece;
    }

    /**
     * getHistory
     *
     * @return Collection<int, History>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    /**
     * addHistory
     *
     * @param History $history
     * @return self
     */
    public function addHistory(History $history): self
    {
        if (!$this->history->contains($history)) {
            $this->history[] = $history;
            $history->setGame($this);
        }

        return $this;
    }

    /**
     * removeHistory
     *
     * @param History $history
     * @return self
     */
    public function removeHistory(History $history): self
    {
        if ($this->history->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getGame() === $this) {
                $history->setGame(null);
            }
        }

        return $this;
    }

    /**
     * getBoard
     *
     * @return Board|null
     */
    public function getBoard(): ?Board
    {
        return $this->board;
    }

    /**
     * setBoard
     *
     * @param Board|null $board
     * @return self
     */
    public function setBoard(?Board $board): self
    {
        $this->board = $board;

        return $this;
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->board->getType();
    }

    /**
     * getPieces
     *
     * @return Collection<i, Piece>
     */
    public function getPieces(): Collection
    {
        $pieces = [];
        foreach ($this->getPlayer() as $player) {
            foreach ($player->getPieces() as $piece) {
                $pieces[] = $piece;
            }
        }

        return new ArrayCollection($pieces);
    }

    /**
     * isFinished
     *
     * @return boolean
     */
    public function isFinished(): bool
    {
        foreach ($this->player as $player) {
            $playerHasPieces = !$player->getPieces()->isEmpty();
            if ($playerHasPieces) {
                continue;
            }

            return true;
        }

        return false;
    }
}
