<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Entity\Repository;

use Oro\Bundle\TaskBundle\Entity\Repository\TaskRepository;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;

class TaskRepositoryTest extends WebTestCase
{
    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient();
        $this->loadFixtures(
            [
                '@OroTaskBundle/Tests/Functional/DataFixtures/task_data.yml'
            ]
        );
        $registry = static::getContainer()->get('doctrine');
        $this->taskRepository = $registry->getRepository(Task::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testGetTasksAssignedTo()
    {
        $taskOwner = $this->getReference(LoadUserData::SIMPLE_USER);
        $assignedTasks = $this->taskRepository->getTasksAssignedTo($taskOwner, 10);
        $this->assertIsArray($assignedTasks);
    }

    /**
     * {@inheritdoc}
     */
    public function testGetTaskListByTimeIntervalQueryBuilderWithExtraFields()
    {
        $userId = 123;
        $startDate = new \DateTime();
        $endDate = clone $startDate;
        $endDate->add(new \DateInterval('P1D'));
        $extraFields = ['status'];

        $qb = $this->taskRepository->getTaskListByTimeIntervalQueryBuilder($userId, $startDate, $endDate, $extraFields);
        self::assertEquals($userId, $qb->getParameter('assignedTo')->getValue());
        self::assertEquals($startDate, $qb->getParameter('start')->getValue());
        self::assertEquals($endDate, $qb->getParameter('end')->getValue());
    }

    /**
     * {@inheritdoc}
     */
    public function testGetTaskListByTimeIntervalQueryBuilderWithoutExtraFields()
    {
        $userId = 123;
        $startDate = new \DateTime();
        $endDate = clone $startDate;
        $endDate->add(new \DateInterval('P1D'));

        $qb = $this->taskRepository->getTaskListByTimeIntervalQueryBuilder($userId, $startDate, $endDate);
        self::assertEquals($userId, $qb->getParameter('assignedTo')->getValue());
        self::assertEquals($startDate, $qb->getParameter('start')->getValue());
        self::assertEquals($endDate, $qb->getParameter('end')->getValue());
    }
}
