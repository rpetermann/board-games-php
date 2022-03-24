<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * GameRuleCompilerPass
 */
class GameRuleCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $contextDefinition = $container->findDefinition('App\\Services\\GameRule\\GameRule')->setPublic(true);

        $gameRuleHandlerIds = array_keys(
            $container->findTaggedServiceIds('game_rule.handler')
        );

        foreach ($gameRuleHandlerIds as $gameHandler) {
            $contextDefinition->addMethodCall(
                'addHandler',
                [new Reference($gameHandler)]
            );
        }
    }
}
