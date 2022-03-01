<?php

namespace App\Dto;

use App\Entity\AbstractEntity;
use App\Entity\Game;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * HistoryDto
 */
class HistoryDto
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var Game
     */
    protected $game;

    /**
     * __construct
     *
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $this->serializer = new Serializer([$normalizer]);

        $this->game = $game;
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'game' => $this->normalize($this->game),
        ];
    }

    /**
     * normalize
     *
     * @param AbstractEntity $entity
     * @return array
     */
    protected function normalize(AbstractEntity $entity): array
    {
        return $this->serializer->normalize($entity, null, ['groups' => ['game', 'default']]);
    }
}
