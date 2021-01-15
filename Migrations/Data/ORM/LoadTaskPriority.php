<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;

/**
 * Loads tasks priority initial data.
 */
class LoadTaskPriority extends AbstractFixture
{
    const PRIORITY_NAME_LOW = 'low';
    const PRIORITY_NAME_NORMAL = 'normal';
    const PRIORITY_NAME_HIGH = 'high';

    /**
     * @var array
     */
    protected $data = [
        [
            'label' => 'Low',
            'name' => self::PRIORITY_NAME_LOW,
            'order' => 1,
        ],
        [
            'label' => 'Normal',
            'name' => self::PRIORITY_NAME_NORMAL,
            'order' => 2,
        ],
        [
            'label' => 'High',
            'name' => self::PRIORITY_NAME_HIGH,
            'order' => 3,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $priority) {
            if (!$this->isPriorityExist($manager, $priority['name'])) {
                $entity = new TaskPriority($priority['name']);
                $entity->setLabel($priority['label']);
                $entity->setOrder($priority['order']);
                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string $priorityType
     * @return bool
     */
    private function isPriorityExist(ObjectManager $manager, $priorityType)
    {
        return null !== $manager->getRepository('OroTaskBundle:TaskPriority')->find($priorityType);
    }
}
