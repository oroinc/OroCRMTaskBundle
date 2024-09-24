<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\UpdateWithOrganization;
use Oro\Bundle\TaskBundle\Entity\Task;

/**
 * Sets a default organization to Task entity.
 */
class UpdateTaskWithOrganization extends UpdateWithOrganization implements DependentFixtureInterface
{
    #[\Override]
    public function getDependencies(): array
    {
        return [LoadOrganizationAndBusinessUnitData::class];
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->update($manager, Task::class);
    }
}
