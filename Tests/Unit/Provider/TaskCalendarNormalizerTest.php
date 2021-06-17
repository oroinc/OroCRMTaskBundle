<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Provider;

use Doctrine\ORM\AbstractQuery;
use Oro\Bundle\ReminderBundle\Entity\Manager\ReminderManager;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Provider\TaskCalendarNormalizer;

class TaskCalendarNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ReminderManager|\PHPUnit\Framework\MockObject\MockObject */
    private $reminderManager;

    /** @var TaskCalendarNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->reminderManager = $this->createMock(ReminderManager::class);

        $this->normalizer = new TaskCalendarNormalizer($this->reminderManager);
    }

    /**
     * @dataProvider getTasksProvider
     */
    public function testGetTasks(array $tasks, array $expected)
    {
        $calendarId = 123;

        $query = $this->createMock(AbstractQuery::class);

        $query->expects($this->once())
            ->method('getArrayResult')
            ->willReturn($tasks);

        $this->reminderManager->expects($this->once())
            ->method('applyReminders')
            ->with($expected, Task::class);

        $result = $this->normalizer->getTasks($calendarId, $query);
        self::assertEquals($expected, $result);
    }

    public function getTasksProvider(): array
    {
        $createdDate = new \DateTime();
        $updatedDate = $createdDate->add(new \DateInterval('PT10S'));
        $startDate   = $createdDate->add(new \DateInterval('PT1H'));
        $end         = clone($startDate);
        $endDate     = $end->add(new \DateInterval('PT30M'));

        return [
            [
                'tasks'    => [
                    [
                        'id'          => 1,
                        'subject'     => 'test_subject',
                        'description' => 'test_description',
                        'dueDate'     => $startDate,
                        'createdAt'   => $createdDate,
                        'updatedAt'   => $updatedDate,
                    ]
                ],
                'expected' => [
                    [
                        'calendar'    => 123,
                        'id'          => 1,
                        'title'       => 'test_subject',
                        'description' => 'test_description',
                        'start'       => $startDate->format('c'),
                        'end'         => $endDate->format('c'),
                        'allDay'      => false,
                        'createdAt'   => $createdDate->format('c'),
                        'updatedAt'   => $updatedDate->format('c'),
                        'editable'    => false,
                        'removable'   => false,
                    ]
                ],
            ],
        ];
    }
}
