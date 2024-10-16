<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;

class LoadTaskPriorityData extends AbstractFixture
{
    public const TASK_PRIORITY_LOW = 'task_priority_low';
    public const TASK_PRIORITY_NORMAL = 'task_priority_normal';
    public const TASK_PRIORITY_HIGH = 'task_priority_high';

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository(TaskPriority::class);
        /** @var TaskPriority[] $priorities */
        $priorities = $repository->findBy(['name' => ['low', 'normal', 'high']]);
        foreach ($priorities as $priority) {
            $this->setReference('task_priority_' . $priority->getName(), $priority);
        }
    }
}
