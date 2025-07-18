<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\TaskBundle\DependencyInjection\OroTaskExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroTaskExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'prod');

        $extension = new OroTaskExtension();
        $extension->load([], $container);

        self::assertNotEmpty($container->getDefinitions());

        self::assertTrue($container->getParameter('oro_task.calendar_provider.my_tasks.enabled'));
    }

    public function testLoadWithCustomConfigs(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'prod');

        $configs = [
            ['my_tasks_in_calendar' => false]
        ];

        $extension = new OroTaskExtension();
        $extension->load($configs, $container);

        self::assertFalse($container->getParameter('oro_task.calendar_provider.my_tasks.enabled'));
    }
}
