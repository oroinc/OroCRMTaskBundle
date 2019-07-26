<?php

namespace Oro\Bundle\TaskBundle\Provider;

use Oro\Bundle\CalendarBundle\Provider\AbstractCalendarProvider;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\TaskBundle\Entity\Repository\TaskRepository;
use Oro\Bundle\TaskBundle\Entity\Task;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides a way to show tasks in the calendar
 */
class TaskCalendarProvider extends AbstractCalendarProvider
{
    const ALIAS                = 'tasks';
    const MY_TASKS_CALENDAR_ID = 1;

    /** @var AclHelper */
    protected $aclHelper;

    /** @var TaskCalendarNormalizer */
    protected $taskCalendarNormalizer;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var bool */
    protected $myTasksEnabled;

    /** @var  bool */
    protected $calendarLabels = [
        self::MY_TASKS_CALENDAR_ID => 'oro.task.menu.my_tasks'
    ];

    /**
     * @param DoctrineHelper         $doctrineHelper
     * @param AclHelper              $aclHelper
     * @param TaskCalendarNormalizer $taskCalendarNormalizer
     * @param TranslatorInterface    $translator
     * @param bool                   $myTasksEnabled
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        AclHelper $aclHelper,
        TaskCalendarNormalizer $taskCalendarNormalizer,
        TranslatorInterface $translator,
        $myTasksEnabled
    ) {
        parent::__construct($doctrineHelper);
        $this->aclHelper              = $aclHelper;
        $this->taskCalendarNormalizer = $taskCalendarNormalizer;
        $this->translator             = $translator;
        $this->myTasksEnabled         = $myTasksEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalendarDefaultValues($organizationId, $userId, $calendarId, array $calendarIds)
    {
        $result = [];

        if ($this->myTasksEnabled) {
            $result[self::MY_TASKS_CALENDAR_ID] = [
                'calendarName'    => $this->translator->trans($this->calendarLabels[self::MY_TASKS_CALENDAR_ID]),
                'removable'       => false,
                'position'        => -100,
                'backgroundColor' => '#F83A22',
                'options'         => [
                    'widgetRoute'   => 'oro_task_widget_info',
                    'widgetOptions' => [
                        'title'         => $this->translator->trans('oro.task.info_widget_title'),
                        'dialogOptions' => [
                            'width' => 600
                        ]
                    ]
                ]
            ];
        } elseif (in_array(self::MY_TASKS_CALENDAR_ID, $calendarIds)) {
            $result[self::MY_TASKS_CALENDAR_ID] = null;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalendarEvents(
        $organizationId,
        $userId,
        $calendarId,
        $start,
        $end,
        $connections,
        $extraFields = []
    ) {
        if (!$this->myTasksEnabled) {
            return [];
        }

        if ($this->isCalendarVisible($connections, self::MY_TASKS_CALENDAR_ID)) {
            /** @var TaskRepository $repo */
            $repo        = $this->doctrineHelper->getEntityRepository(Task::class);
            $extraFields = $this->filterSupportedFields($extraFields, Task::class);
            $qb          = $repo->getTaskListByTimeIntervalQueryBuilder($userId, $start, $end, $extraFields);
            $query       = $this->aclHelper->apply($qb);

            return $this->taskCalendarNormalizer->getTasks(self::MY_TASKS_CALENDAR_ID, $query);
        }

        return [];
    }

    /**
     * @param array $connections
     * @param int   $calendarId
     * @param bool  $default
     *
     * @return bool
     */
    protected function isCalendarVisible($connections, $calendarId, $default = true)
    {
        return isset($connections[$calendarId])
            ? $connections[$calendarId]
            : $default;
    }
}
