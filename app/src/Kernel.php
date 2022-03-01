<?php

namespace App;

use App\DependencyInjection\GameRuleCompilerPass;
use App\DependencyInjection\PieceRuleCompilerPass;
use App\Services\GameRule\RuleHandler\GameHandlerInterface;
use App\Services\GameRule\RuleHandler\PieceHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const SERVICES_INJECTION = [
        [
            'interface' => GameHandlerInterface::class,
            'tag' => 'game_rule.handler',
            'compilerPass' => GameRuleCompilerPass::class,
        ],
        [
            'interface' => PieceHandlerInterface::class,
            'tag' => 'piece_rule.handler',
            'compilerPass' => PieceRuleCompilerPass::class,
        ],
    ];

    /**
     * @inheritDoc
     */
    protected function build(ContainerBuilder $container): void
    {
        foreach (self::SERVICES_INJECTION as $service) {
            $container->registerForAutoconfiguration($service['interface'])
                ->addTag($service['tag']);

            $container->addCompilerPass(new $service['compilerPass']);
        }
    }
}
