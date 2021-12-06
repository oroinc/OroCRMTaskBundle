<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Entity\Repository;

use Oro\Bundle\TaskBundle\Entity\Repository\TaskRepository;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;

class TaskRepositoryTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient();
        $this->loadFixtures(['@OroTaskBundle/Tests/Functional/DataFixtures/task_data.yml']);
    }

    private function getTaskRepository(): TaskRepository
    {
        return self::getContainer()->get('doctrine')->getRepository(Task::class);
    }

    public function testGetTasksAssignedTo()
    {
        $taskOwner = $this->getReference(LoadUserData::SIMPLE_USER);
        $assignedTasks = $this->getTaskRepository()->getTasksAssignedTo($taskOwner, 10);
        $this->assertIsArray($assignedTasks);
    }

    public function testGetTaskListByTimeIntervalQueryBuilderWithExtraFields()
    {
        $userId = 123;
        $startDate = new \DateTime();
        $endDate = clone $startDate;
        $endDate->add(new \DateInterval('P1D'));
        $extraFields = ['status'];

        $qb = $this->getTaskRepository()->getTaskListByTimeIntervalQueryBuilder(
            $userId,
            $startDate,
            $endDate,
            $extraFields
        );
        self::assertEquals($userId, $qb->getParameter('assignedTo')->getValue());
        self::assertEquals($startDate, $qb->getParameter('start')->getValue());
        self::assertEquals($endDate, $qb->getParameter('end')->getValue());
    }

    public function testGetTaskListByTimeIntervalQueryBuilderWithoutExtraFields()
    {
        $userId = 123;
        $startDate = new \DateTime();
        $endDate = clone $startDate;
        $endDate->add(new \DateInterval('P1D'));

        $qb = $this->getTaskRepository()->getTaskListByTimeIntervalQueryBuilder($userId, $startDate, $endDate);
        self::assertEquals($userId, $qb->getParameter('assignedTo')->getValue());
        self::assertEquals($startDate, $qb->getParameter('start')->getValue());
        self::assertEquals($endDate, $qb->getParameter('end')->getValue());
    }
}
