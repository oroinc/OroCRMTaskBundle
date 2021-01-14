<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTaskPriorityData extends AbstractFixture implements ContainerAwareInterface
{
    public const TASK_PRIORITY_LOW = 'task_priority_low';
    public const TASK_PRIORITY_NORMAL = 'task_priority_normal';
    public const TASK_PRIORITY_HIGH = 'task_priority_high';

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
        $lowPriority = $manager->find(TaskPriority::class, 'low');
        $normalPriority = $manager->find(TaskPriority::class, 'normal');
        $highPriority = $manager->find(TaskPriority::class, 'high');

        $this->setReference(self::TASK_PRIORITY_LOW, $lowPriority);
        $this->setReference(self::TASK_PRIORITY_NORMAL, $normalPriority);
        $this->setReference(self::TASK_PRIORITY_HIGH, $highPriority);
    }
}
