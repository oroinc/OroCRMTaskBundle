<?php

namespace Oro\Bundle\TaskBundle\Tests\Behat;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Nelmio\Alice\Instances\Collection as AliceCollection;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TestFrameworkBundle\Behat\Isolation\ReferenceRepositoryInitializerInterface;

class ReferenceRepositoryInitializer implements ReferenceRepositoryInitializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function init(Registry $doctrine, AliceCollection $referenceRepository)
    {
        $this->setTaskPriorityReferences($doctrine, $referenceRepository);
    }

    /**
     * @param Registry $doctrine
     * @param AliceCollection $referenceRepository
     */
    private function setTaskPriorityReferences(Registry $doctrine, AliceCollection $referenceRepository)
    {
        $taskPriorityRepository = $doctrine
            ->getManagerForClass(TaskPriority::class)
            ->getRepository(TaskPriority::class);

        $lowPriority = $taskPriorityRepository->find('low');
        $referenceRepository->set('task_priority_low', $lowPriority);

        $normalPriority = $taskPriorityRepository->find('normal');
        $referenceRepository->set('task_priority_normal', $normalPriority);

        $highPriority = $taskPriorityRepository->find('high');
        $referenceRepository->set('task_priority_high', $highPriority);
    }
}
