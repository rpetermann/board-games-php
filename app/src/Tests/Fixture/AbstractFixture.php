<?php

namespace App\Tests\Fixture;

use App\Entity\AbstractEntity;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Uid\Uuid;

/**
 * AbstractFixture
 */
abstract class AbstractFixture
{
    /**
     * setEntityProperties
     *
     * @param  AbstractEntity $entity
     * @param  array          $parameters
     * @return void
     */
    protected static function setEntityProperties(AbstractEntity &$entity, array $parameters): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($parameters as $property => $value) {
            if (is_null($value) || !$propertyAccessor->isWritable($entity, $property)) {
                continue;
            }

            $propertyAccessor->setValue($entity, $property, $value);
        }
    }

    /**
     * generateUuid
     *
     * @return string
     */
    protected static function generateUuid(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
