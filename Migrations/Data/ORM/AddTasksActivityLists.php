<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ActivityListBundle\Migrations\Data\ORM\AddActivityListsData;
use Oro\Bundle\TaskBundle\Entity\Task;

/**
 * Adds activity lists for Task entity.
 */
class AddTasksActivityLists extends AddActivityListsData implements DependentFixtureInterface
{
    #[\Override]
    public function getDependencies(): array
    {
        return [UpdateTaskWithOrganization::class];
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->addActivityListsForActivityClass(
            $manager,
            Task::class,
            'owner',
            'organization'
        );
    }
}
