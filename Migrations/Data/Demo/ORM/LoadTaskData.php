<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Entity\EnumOption;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TaskBundle\Migrations\Data\ORM\LoadTaskPriority;
use Oro\Bundle\TaskBundle\Migrations\Data\ORM\LoadTaskStatusOptionData;
use Oro\Bundle\UserBundle\DataFixtures\UserUtilityTrait;

/**
 * Loading demo data for Task entity
 */
class LoadTaskData extends AbstractFixture implements DependentFixtureInterface
{
    use UserUtilityTrait;

    /**
     * Load issues
     */
    #[\Override]
    public function load(ObjectManager $manager)
    {
        $user = $this->getFirstUser($manager);
        $enumOptionId = ExtendHelper::buildEnumOptionId('task_status', 'open');
        $status = $manager->find(EnumOption::class, $enumOptionId);
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

    #[\Override]
    public function getDependencies(): array
    {
        return [LoadTaskStatusOptionData::class];
    }
}
