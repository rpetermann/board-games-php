<?php

namespace App\Factory;

use App\Entity\AbstractEntity;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * AbstractFactory
 */
abstract class AbstractEntityFactory
{
    /**
     * @var PropertyAccessor $propertyAccessor
     */
    protected $propertyAccessor;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * setEntityValue
     *
     * @param AbstractEntity $entity
     * @param array          $data
     * @return void
     */
    protected function setEntityValue(AbstractEntity &$entity, array $data): void
    {
        foreach ($data as $field => $value) {
            if (is_null($value) || !$this->propertyAccessor->isWritable($entity, $field)) {
                continue;
            }

            $this->propertyAccessor->setValue($entity, $field, $value);
        }
    }
}
