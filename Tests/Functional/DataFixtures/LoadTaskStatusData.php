<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class LoadTaskStatusData extends AbstractFixture
{
    public const TASK_STATUS_OPEN = 'task_status_open';
    public const TASK_STATUS_IN_PROGRESS = 'task_status_in_progress';
    public const TASK_STATUS_CLOSED = 'task_status_closed';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository(ExtendHelper::buildEnumValueClassName('task_status'));
        /** @var AbstractEnumValue[] $statuses */
        $statuses = $repository->findBy(['id' => ['open', 'in_progress', 'closed']]);
        foreach ($statuses as $status) {
            $this->setReference('task_status_' . $status->getId(), $status);
        }
    }
}
