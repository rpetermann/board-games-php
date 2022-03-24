<?php

namespace App\DoctrineExtensions\SQLFilter;

use Prophecy\Argument\Token\TokenInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * TokenFilter
 */
class TokenFilter extends SQLFilter
{
    /**
     * @inheritDoc
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->reflClass->implementsInterface('App\Entity\TokenInterface')) {
            return "";
        }

        return $targetTableAlias.'.access_token = '.$this->getParameter('token');
    }
}
