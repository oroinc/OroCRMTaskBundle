<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Provider;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\TaskBundle\Entity\Repository\TaskRepository;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Provider\TaskCalendarNormalizer;
use Oro\Bundle\TaskBundle\Provider\TaskCalendarProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskCalendarProviderTest extends TestCase
{
    private DoctrineHelper&MockObject $doctrineHelper;
    private AclHelper&MockObject $aclHelper;
    private TaskCalendarNormalizer&MockObject $taskCalendarNormalizer;
    private TranslatorInterface&MockObject $translator;
    private TaskCalendarProvider $provider;

    #[\Override]
    protected function setUp(): void
    {
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);
        $this->aclHelper = $this->createMock(AclHelper::class);
        $this->taskCalendarNormalizer = $this->createMock(TaskCalendarNormalizer::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->provider = new TaskCalendarProvider(
            $this->doctrineHelper,
            $this->aclHelper,
            $this->taskCalendarNormalizer,
            $this->translator,
            true
        );
    }

    public function testGetCalendarDefaultValuesDisabled(): void
    {
        $organizationId = 1;
        $userId = 123;
        $calendarId = 10;
        $calendarIds = [TaskCalendarProvider::MY_TASKS_CALENDAR_ID];

        $provider = new TaskCalendarProvider(
            $this->doctrineHelper,
            $this->aclHelper,
            $this->taskCalendarNormalizer,
            $this->translator,
            false
        );

        $result = $provider->getCalendarDefaultValues($organizationId, $userId, $calendarId, $calendarIds);
        self::assertEquals(
            [
                TaskCalendarProvider::MY_TASKS_CALENDAR_ID => null,
            ],
            $result
        );
    }

    public function testGetCalendarDefaultValues(): void
    {
        $organizationId = 1;
        $userId = 123;
        $calendarId = 10;

        $this->translator->expects($this->exactly(2))
            ->method('trans')
            ->willReturnArgument(0);

        self::assertEquals(
            [
                TaskCalendarProvider::MY_TASKS_CALENDAR_ID => [
                    'calendarName' => 'oro.task.menu.my_tasks',
                    'removable' => false,
                    'position' => -100,
                    'backgroundColor' => '#A92F1F',
                    'options' => [
                        'widgetRoute' => 'oro_task_widget_info',
                        'widgetOptions' => [
                            'title' => 'oro.task.info_widget_title',
                            'dialogOptions' => [
                                'width' => 600,
                            ],
                        ],
                    ],
                ],
            ],
            $this->provider->getCalendarDefaultValues($organizationId, $userId, $calendarId, [])
        );
    }

    /**
     * @dataProvider getCalendarEventsProvider
     */
    public function testGetCalendarEvents(array $connections, array $tasks): void
    {
        $organizationId = 1;
        $userId = 123;
        $calendarId = 10;
        $start = new \DateTime();
        $end = new \DateTime();

        $qb = $this->createMock(QueryBuilder::class);
        $repo = $this->createMock(TaskRepository::class);
        $repo->expects($this->once())
            ->method('getTaskListByTimeIntervalQueryBuilder')
            ->with($userId, $this->identicalTo($start), $this->identicalTo($end))
            ->willReturn($qb);

        $query = $this->createMock(AbstractQuery::class);
        $this->aclHelper->expects($this->once())
            ->method('apply')
            ->with($this->identicalTo($qb))
            ->willReturn($query);

        $this->doctrineHelper->expects($this->once())
            ->method('getEntityRepository')
            ->with(Task::class)
            ->willReturn($repo);

        $this->taskCalendarNormalizer->expects($this->once())
            ->method('getTasks')
            ->with(TaskCalendarProvider::MY_TASKS_CALENDAR_ID, $this->identicalTo($query))
            ->willReturn($tasks);

        $result = $this->provider->getCalendarEvents($organizationId, $userId, $calendarId, $start, $end, $connections);
        self::assertEquals($tasks, $result);
    }

    public function getCalendarEventsProvider(): array
    {
        return [
            'no connections' => [
                'connections' => [],
                'tasks' => [['id' => 1]],
            ],
            'with visible connection' => [
                'connections' => [
                    [TaskCalendarProvider::MY_TASKS_CALENDAR_ID => true],
                ],
                'tasks' => [['id' => 1]],
            ],
        ];
    }

    public function testGetCalendarEventsWithInvisibleConnection(): void
    {
        $organizationId = 1;
        $userId = 123;
        $calendarId = 10;
        $start = new \DateTime();
        $end = new \DateTime();
        $connections = [TaskCalendarProvider::MY_TASKS_CALENDAR_ID => false];

        $this->taskCalendarNormalizer->expects($this->never())
            ->method('getTasks');

        self::assertSame(
            [],
            $this->provider->getCalendarEvents($organizationId, $userId, $calendarId, $start, $end, $connections)
        );
    }
}
