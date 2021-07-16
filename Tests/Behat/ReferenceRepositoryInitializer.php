<?php

namespace Oro\Bundle\TaskBundle\Tests\Behat;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TestFrameworkBundle\Behat\Isolation\ReferenceRepositoryInitializerInterface;
use Oro\Bundle\TestFrameworkBundle\Test\DataFixtures\Collection;

class ReferenceRepositoryInitializer implements ReferenceRepositoryInitializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function init(Registry $doctrine, Collection $referenceRepository)
    {
        $this->setTaskPriorityReferences($doctrine, $referenceRepository);
        $this->setTaskStatusReferences($doctrine, $referenceRepository);
    }

    private function setTaskPriorityReferences(Registry $doctrine, Collection $referenceRepository)
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

    private function setTaskStatusReferences(Registry $doctrine, Collection $referenceRepository)
    {
        $enumClass = ExtendHelper::buildEnumValueClassName('task_status');

        $repository = $doctrine->getManagerForClass($enumClass)->getRepository($enumClass);

        /** @var AbstractEnumValue $status */
        foreach ($repository->findAll() as $status) {
            $referenceRepository->set(sprintf('task_status_%s', $status->getId()), $status);
        }
    }
}
