<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;

/**
 * Loads tasks priority initial data.
 */
class LoadTaskPriority extends AbstractFixture
{
    public const PRIORITY_NAME_LOW = 'low';
    public const PRIORITY_NAME_NORMAL = 'normal';
    public const PRIORITY_NAME_HIGH = 'high';

    private array $data = [
        [
            'label' => 'Low',
            'name' => self::PRIORITY_NAME_LOW,
            'order' => 1,
        ],
        [
            'label' => 'Normal',
            'name' => self::PRIORITY_NAME_NORMAL,
            'order' => 2,
        ],
        [
            'label' => 'High',
            'name' => self::PRIORITY_NAME_HIGH,
            'order' => 3,
        ],
    ];

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data as $priority) {
            if (!$this->isPriorityExist($manager, $priority['name'])) {
                $entity = new TaskPriority($priority['name']);
                $entity->setLabel($priority['label']);
                $entity->setOrder($priority['order']);
                $manager->persist($entity);
            }
        }
        $manager->flush();
    }

    private function isPriorityExist(ObjectManager $manager, string $priorityType): bool
    {
        return null !== $manager->getRepository(TaskPriority::class)->find($priorityType);
    }
}
