<?php

namespace Oro\Bundle\TaskBundle\Tests\Behat;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\EntityExtendBundle\Entity\EnumOption;
use Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TestFrameworkBundle\Behat\Isolation\ReferenceRepositoryInitializerInterface;
use Oro\Bundle\TestFrameworkBundle\Test\DataFixtures\Collection;

class ReferenceRepositoryInitializer implements ReferenceRepositoryInitializerInterface
{
    #[\Override]
    public function init(ManagerRegistry $doctrine, Collection $referenceRepository): void
    {
        $this->setTaskPriorityReferences($doctrine, $referenceRepository);
        $this->setTaskStatusReferences($doctrine, $referenceRepository);
    }

    private function setTaskPriorityReferences(ManagerRegistry $doctrine, Collection $referenceRepository): void
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

    private function setTaskStatusReferences(ManagerRegistry $doctrine, Collection $referenceRepository): void
    {
        $repository = $doctrine->getManagerForClass(EnumOption::class)->getRepository(EnumOption::class);
        /** @var EnumOptionInterface $status */
        foreach ($repository->findBy(['enumCode' => 'task_status']) as $status) {
            $referenceRepository->set(\sprintf('task_status_%s', $status->getInternalId()), $status);
        }
    }
}
