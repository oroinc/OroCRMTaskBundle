<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Environment;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\EntityBundle\Tests\Functional\Environment\TestEntityNameResolverDataLoaderInterface;
use Oro\Bundle\EntityExtendBundle\Entity\EnumOption;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;

class TestEntityNameResolverDataLoader implements TestEntityNameResolverDataLoaderInterface
{
    private TestEntityNameResolverDataLoaderInterface $innerDataLoader;

    public function __construct(TestEntityNameResolverDataLoaderInterface $innerDataLoader)
    {
        $this->innerDataLoader = $innerDataLoader;
    }

    public function loadEntity(
        EntityManagerInterface $em,
        ReferenceRepository $repository,
        string $entityClass
    ): array {
        if (Task::class === $entityClass) {
            $task = new Task();
            $task->setOrganization($repository->getReference('organization'));
            $task->setOwner($repository->getReference('user'));
            $task->setTaskPriority($em->find(TaskPriority::class, 'high'));
            $task->setStatus($em->find(
                EnumOption::class,
                ExtendHelper::buildEnumOptionId('task_status', 'open')
            ));
            $task->setDueDate(new \DateTime('2023-03-28 12:10:05', new \DateTimeZone('UTC')));
            $task->setSubject('Test Task');
            $repository->setReference('task', $task);
            $em->persist($task);
            $em->flush();

            return ['task'];
        }

        return $this->innerDataLoader->loadEntity($em, $repository, $entityClass);
    }

    public function getExpectedEntityName(
        ReferenceRepository $repository,
        string $entityClass,
        string $entityReference,
        ?string $format,
        ?string $locale
    ): string {
        if (Task::class === $entityClass) {
            return 'Test Task';
        }

        return $this->innerDataLoader->getExpectedEntityName(
            $repository,
            $entityClass,
            $entityReference,
            $format,
            $locale
        );
    }
}
