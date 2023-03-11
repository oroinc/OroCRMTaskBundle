<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\TaskBundle\DependencyInjection\OroTaskExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroTaskExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $extension = new OroTaskExtension();
        $extension->load([], $container);

        self::assertNotEmpty($container->getDefinitions());

        self::assertTrue($container->getParameter('oro_task.calendar_provider.my_tasks.enabled'));
    }

    public function testLoadWithCustomConfigs(): void
    {
        $container = new ContainerBuilder();

        $configs = [
            ['my_tasks_in_calendar' => false]
        ];

        $extension = new OroTaskExtension();
        $extension->load($configs, $container);

        self::assertFalse($container->getParameter('oro_task.calendar_provider.my_tasks.enabled'));
    }
}
