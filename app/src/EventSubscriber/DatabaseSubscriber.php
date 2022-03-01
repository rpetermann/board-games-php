<?php

namespace App\EventSubscriber;

use App\Entity\AbstractEntity;
use App\Validator\Exception\ValidatorException;
use App\Validator\Validator;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

/**
 * DatabaseSubscriber
 */
class DatabaseSubscriber implements EventSubscriberInterface
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * __construct
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * prePersist
     *
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->validate($event->getEntity());
    }

    /**
     * preUpdate
     *
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->validate($event->getEntity());
    }

    /**
     * validate
     *
     * @param AbstractEntity $entity
     * @return void
     */
    public function validate(AbstractEntity $entity): void
    {
        $errors = $this->validator->validate($entity);

        if ($errors->count()) {
            throw ValidatorException::fromValidationError((string) $errors);
        }
    }    
}
