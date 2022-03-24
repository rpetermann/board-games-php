<?php

namespace App\Controller;

use App\Exception\GameException;
use App\Model\GameAddPlayerModel;
use App\Model\GameAllowedPieceMovesModel;
use App\Model\GameCreateModel;
use App\Model\GameMovePieceModel;
use App\Model\GameStartModel;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * GameController
 */
class GameController extends AbstractController
{
    /**
     * create
     *
     * @param Request         $request
     * @param GameCreateModel $gameCreateModel
     * @return JsonResponse
     */
    public function create(Request $request, GameCreateModel $gameCreateModel): JsonResponse
    {
        $payload = $this->getJsonContentByRequest($request);

        $game = $gameCreateModel->process($payload);

        return $this->json($game, JsonResponse::HTTP_OK, [], ['groups' => ['game', 'token', 'default']]);
    }

    /**
     * addPlayer
     *
     * @param Request            $request
     * @param string             $gameId
     * @param GameAddPlayerModel $gameAddPlayerModel
     * @return JsonResponse
     */
    public function addPlayer(Request $request, string $gameId, GameAddPlayerModel $gameAddPlayerModel): JsonResponse
    {
        $payload = $this->getJsonContentByRequest($request);

        $game = $gameAddPlayerModel->process($gameId, $payload);

        return $this->json($game, JsonResponse::HTTP_OK, [], ['groups' => ['game', 'default']]);
    }

    /**
     * start
     *
     * @param string         $gameId
     * @param GameStartModel $gameStartModel
     * @return JsonResponse
     */
    public function start(string $gameId, GameStartModel $gameStartModel): JsonResponse
    {
        $game = $gameStartModel->process($gameId);

        return $this->json($game, JsonResponse::HTTP_OK, [], ['groups' => ['game', 'default']]);
    }

    /**
     * movePiece
     *
     * @param Request            $request
     * @param string             $gameId
     * @param string             $playerId
     * @param string             $pieceId
     * @param GameMovePieceModel $gameMovePieceModel
     * @return JsonResponse
     */
    public function movePiece(Request $request, string $gameId, string $playerId, string $pieceId, GameMovePieceModel $gameMovePieceModel): JsonResponse
    {
        $payload = $this->getJsonContentByRequest($request);

        if (!isset($payload['toX']) || !isset($payload['toY'])) {
            throw new GameException(GameException::TYPE_INVALID_PAYLOAD);
        }

        $game = $gameMovePieceModel->process($gameId, $playerId, $pieceId, $payload);

        return $this->json($game, JsonResponse::HTTP_OK, [], ['groups' => ['game', 'default']]);
    }

    /**
     * read
     *
     * @param string $gameId
     * @return JsonResponse
     */
    public function read(string $gameId, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->getOneById($gameId);

        return $this->json($game, JsonResponse::HTTP_OK, [], ['groups' => ['game', 'default']]);
    }

    /**
     * readAllowedPieceMoves
     *
     * @param string                     $gameId
     * @param string                     $playerId
     * @param string                     $pieceId
     * @param GameAllowedPieceMovesModel $gameAllowedPieceMovesModel
     * @return JsonResponse
     */
    public function readAllowedPieceMoves(string $gameId, string $playerId, string $pieceId, GameAllowedPieceMovesModel $gameAllowedPieceMovesModel): JsonResponse
    {
        $allowedPieceMoves = $gameAllowedPieceMovesModel->process($gameId, $playerId, $pieceId);

        return $this->json($allowedPieceMoves, JsonResponse::HTTP_OK);
    }
}
