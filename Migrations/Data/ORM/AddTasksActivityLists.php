<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ActivityListBundle\Migrations\Data\ORM\AddActivityListsData;
use Oro\Bundle\TaskBundle\Entity\Task;

/**
 * Adding activity lists for Task entity
 */
class AddTasksActivityLists extends AddActivityListsData implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [UpdateTaskWithOrganization::class];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->addActivityListsForActivityClass(
            $manager,
            Task::class,
            'owner',
            'organization'
        );
    }
}
