<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Entity\EnumOption;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class LoadTaskStatusData extends AbstractFixture
{
    public const TASK_STATUS_OPEN = 'task_status_open';
    public const TASK_STATUS_IN_PROGRESS = 'task_status_in_progress';
    public const TASK_STATUS_CLOSED = 'task_status_closed';

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $statusOpen = $manager->find(EnumOption::class, ExtendHelper::buildEnumOptionId('task_status', 'open'));
        $statusInProgress = $manager->find(
            EnumOption::class,
            ExtendHelper::buildEnumOptionId('task_status', 'in_progress')
        );
        $statusClosed = $manager->find(EnumOption::class, ExtendHelper::buildEnumOptionId('task_status', 'closed'));

        $this->setReference(self::TASK_STATUS_OPEN, $statusOpen);
        $this->setReference(self::TASK_STATUS_IN_PROGRESS, $statusInProgress);
        $this->setReference(self::TASK_STATUS_CLOSED, $statusClosed);
    }
}
