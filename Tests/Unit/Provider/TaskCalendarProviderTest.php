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
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskCalendarProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var DoctrineHelper|\PHPUnit\Framework\MockObject\MockObject */
    protected $doctrineHelper;

    /** @var AclHelper|\PHPUnit\Framework\MockObject\MockObject */
    protected $aclHelper;

    /** @var TaskCalendarNormalizer|\PHPUnit\Framework\MockObject\MockObject */
    protected $taskCalendarNormalizer;

    /** @var TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $translator;

    /** @var TaskCalendarProvider */
    protected $provider;

    protected function setUp(): void
    {
        $this->doctrineHelper = $this->getMockBuilder(DoctrineHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->aclHelper = $this->getMockBuilder(AclHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->taskCalendarNormalizer =
            $this->getMockBuilder(TaskCalendarNormalizer::class)
                ->disableOriginalConstructor()
                ->getMock();
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->provider = new TaskCalendarProvider(
            $this->doctrineHelper,
            $this->aclHelper,
            $this->taskCalendarNormalizer,
            $this->translator,
            true
        );
    }

    public function testGetCalendarDefaultValuesDisabled()
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

    public function testGetCalendarDefaultValues()
    {
        $organizationId = 1;
        $userId = 123;
        $calendarId = 10;

        $this->translator->expects($this->exactly(2))
            ->method('trans')
            ->will($this->returnArgument(0));

        self::assertEquals(
            [
                TaskCalendarProvider::MY_TASKS_CALENDAR_ID => [
                    'calendarName' => 'oro.task.menu.my_tasks',
                    'removable' => false,
                    'position' => -100,
                    'backgroundColor' => '#F83A22',
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
    public function testGetCalendarEvents(array $connections, array $tasks)
    {
        $organizationId = 1;
        $userId = 123;
        $calendarId = 10;
        $start = new \DateTime();
        $end = new \DateTime();

        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repo = $this->getMockBuilder(TaskRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repo->expects($this->once())
            ->method('getTaskListByTimeIntervalQueryBuilder')
            ->with($userId, $this->identicalTo($start), $this->identicalTo($end))
            ->will($this->returnValue($qb));

        $query = $this->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->aclHelper->expects($this->once())
            ->method('apply')
            ->with($this->identicalTo($qb))
            ->will($this->returnValue($query));

        $this->doctrineHelper->expects($this->once())
            ->method('getEntityRepository')
            ->with(Task::class)
            ->will($this->returnValue($repo));

        $this->taskCalendarNormalizer->expects($this->once())
            ->method('getTasks')
            ->with(TaskCalendarProvider::MY_TASKS_CALENDAR_ID, $this->identicalTo($query))
            ->will($this->returnValue($tasks));

        $result = $this->provider->getCalendarEvents($organizationId, $userId, $calendarId, $start, $end, $connections);
        self::assertEquals($tasks, $result);
    }

    /**
     * @return array
     */
    public function getCalendarEventsProvider()
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

    public function testGetCalendarEventsWithInvisibleConnection()
    {
        $organizationId = 1;
        $userId = 123;
        $calendarId = 10;
        $start = new \DateTime();
        $end = new \DateTime();
        $connections = [TaskCalendarProvider::MY_TASKS_CALENDAR_ID => false];

        $this->taskCalendarNormalizer->expects($this->never())
            ->method('getTasks');

        $result = $this->provider->getCalendarEvents($organizationId, $userId, $calendarId, $start, $end, $connections);
        self::assertEquals([], $result);
    }
}
