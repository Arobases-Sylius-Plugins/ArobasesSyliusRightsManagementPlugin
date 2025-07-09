<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('arobases_sylius_rights_management');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('group')
            ->children()
                ->arrayNode('groups')
                ->useAttributeAsKey('group_name')
                ->arrayPrototype()
                    ->fixXmlConfig('right')
                    ->children()
                        ->arrayNode('rights')
                            ->defaultValue([])
                            ->useAttributeAsKey('right_name')
                            ->arrayPrototype()
                                ->canBeDisabled()
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->arrayNode('routes')
                                        ->scalarPrototype()->end()
                                    ->end()
                                    ->arrayNode('excludes')
                                        ->scalarPrototype()->end()
                                    ->end()
        ;

        return $treeBuilder;
    }
}
