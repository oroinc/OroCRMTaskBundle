<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\TaskBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $builder       = $configuration->getConfigTreeBuilder();

        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $builder);
    }

    /**
     * @dataProvider processConfigurationDataProvider
     */
    public function testProcessConfiguration($configs, $expected)
    {
        $configuration = new Configuration();
        $processor     = new Processor();
        $this->assertEquals($expected, $processor->processConfiguration($configuration, $configs));
    }

    public function processConfigurationDataProvider()
    {
        return array(
            'empty' => [
                'configs'  => [[]],
                'expected' => [
                    'my_tasks_in_calendar' => true
                ]
            ],
            'filled' => [
                'configs'  => [
                    [
                        'my_tasks_in_calendar' => false
                    ]
                ],
                'expected' => [
                    'my_tasks_in_calendar' => false
                ]
            ],
        );
    }
}
