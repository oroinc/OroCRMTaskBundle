<?php

declare(strict_types=1);

namespace Oro\Bundle\TaskBundle\Tests\Functional\Search;

use Oro\Bundle\EntityExtendBundle\Entity\EnumOption;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SearchBundle\Tests\Functional\Engine\AbstractEntitiesOrmIndexerTest;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures\LoadTaskPriorityData;
use Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures\LoadTaskStatusData;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadOrganization;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadUser;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Tests that Task entities can be indexed without type casting errors with the ORM search engine.
 *
 * @group search
 * @dbIsolationPerTest
 */
class TaskEntitiesOrmIndexerTest extends AbstractEntitiesOrmIndexerTest
{
    #[\Override]
    protected function getSearchableEntityClassesToTest(): array
    {
        return [
            Task::class
        ];
    }

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            LoadOrganization::class,
            LoadUser::class,
            LoadTaskPriorityData::class,
            LoadTaskStatusData::class,
        ]);

        $manager = $this->getDoctrine()->getManagerForClass(Task::class);
        /** @var Organization $organization */
        $organization = $this->getReference(LoadOrganization::ORGANIZATION);
        /** @var User $owner */
        $owner = $this->getReference(LoadUser::USER);
        /** @var TaskPriority $priority */
        $priority = $this->getReference(LoadTaskPriorityData::TASK_PRIORITY_NORMAL);
        /** @var EnumOption $status */
        $status = $this->getReference(LoadTaskStatusData::TASK_STATUS_OPEN);

        $task = new Task();
        $task->setOrganization($organization);
        $task->setOwner($owner);
        $task->setSubject('Test Task');
        $task->setDescription('Test Task Description');
        $task->setTaskPriority($priority);
        $task->setStatus($status);
        $task->setDueDate(new \DateTime('+1 day', new \DateTimeZone('UTC')));
        $this->persistTestEntity($task);

        $manager->flush();
    }
}
