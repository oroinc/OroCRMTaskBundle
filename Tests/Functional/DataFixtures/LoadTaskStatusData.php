<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTaskStatusData extends AbstractFixture implements ContainerAwareInterface
{
    public const TASK_STATUS_OPEN = 'task_status_open';
    public const TASK_STATUS_IN_PROGRESS = 'task_status_in_progress';
    public const TASK_STATUS_CLOSED = 'task_status_closed';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $statusClass = ExtendHelper::buildEnumValueClassName('task_status');

        $statusOpen = $manager->find($statusClass, 'open');
        $statusInProgress = $manager->find($statusClass, 'in_progress');
        $statusClosed = $manager->find($statusClass, 'closed');

        $this->setReference(self::TASK_STATUS_OPEN, $statusOpen);
        $this->setReference(self::TASK_STATUS_IN_PROGRESS, $statusInProgress);
        $this->setReference(self::TASK_STATUS_CLOSED, $statusClosed);
    }
}
