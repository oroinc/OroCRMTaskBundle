<?php

namespace Oro\Bundle\TaskBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    #[\Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('oro_task');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('my_tasks_in_calendar')
                    // please note that if you want to disable it on already working system
                    // you need to take care to create a migration to clean up redundant data
                    // in oro_calendar_property table
                    ->info('Indicates whether My Tasks should be visible in My Calendar or not')
                    ->defaultTrue()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
