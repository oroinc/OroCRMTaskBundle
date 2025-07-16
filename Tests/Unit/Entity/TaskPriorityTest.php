<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Entity;

use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use PHPUnit\Framework\TestCase;

class TaskPriorityTest extends TestCase
{
    use EntityTestCaseTrait;

    public function testProperties(): void
    {
        $properties = [
            ['name', 'someName', false],
            ['label', 'someLabel'],
            ['order', 1]
        ];

        $taskPriority = new TaskPriority('PriorityName');
        self::assertEquals('PriorityName', $taskPriority->getName());

        self::assertPropertyAccessors($taskPriority, $properties);
    }

    public function testToString(): void
    {
        $expectedLabel = 'Low label';
        $entity = new TaskPriority('low');
        $entity->setLabel($expectedLabel);
        self::assertEquals($expectedLabel, (string)$entity);
    }
}
