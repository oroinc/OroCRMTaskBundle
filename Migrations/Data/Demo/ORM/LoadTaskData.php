<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TaskBundle\Migrations\Data\ORM\LoadTaskPriority;
use Oro\Bundle\UserBundle\DataFixtures\UserUtilityTrait;

/**
 * Loading demo data for Task entity
 */
class LoadTaskData extends AbstractFixture
{
    use UserUtilityTrait;

    /**
     * Load issues
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->getFirstUser($manager);
        $status = $manager->find(ExtendHelper::buildEnumValueClassName('task_status'), 'open');
        foreach ($this->getData() as $taskData) {
            $priority = $manager->getRepository(TaskPriority::class)->find($taskData['priority']);

            $task = new Task();
            $task->setSubject($taskData['subject']);
            $task->setDescription($taskData['description']);
            $task->setOwner($user);
            $task->setStatus($status);
            $task->setOrganization($user->getOrganization());
            $task->setTaskPriority($priority);
            $manager->persist($task);
        }
        $manager->flush();
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'subject' => 'Meet James',
                'description' => 'Meet James in the office',
                'priority' => LoadTaskPriority::PRIORITY_NAME_NORMAL,
            ],
            [
                'subject' => 'Check email',
                'description' => '',
                'priority' => LoadTaskPriority::PRIORITY_NAME_LOW,
            ],
            [
                'subject' => 'Open new bank account',
                'description' => 'Go to the bank and open new bank account',
                'priority' => LoadTaskPriority::PRIORITY_NAME_HIGH,
            ],
        ];
    }
}
