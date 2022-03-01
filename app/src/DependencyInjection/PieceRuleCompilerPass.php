<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * PieceRuleCompilerPass
 */
class PieceRuleCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $contextDefinition = $container->findDefinition('App\\Services\\GameRule\\PieceRule')->setPublic(true);

        $pieceRuleHandlerIds = array_keys(
            $container->findTaggedServiceIds('piece_rule.handler')
        );

        foreach ($pieceRuleHandlerIds as $pieceHandler) {
            $contextDefinition->addMethodCall(
                'addHandler',
                [new Reference($pieceHandler)]
            );
        }
    }
}
