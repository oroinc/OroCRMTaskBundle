<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\UpdateWithOrganization;

/**
 * Loading Organizations for Task entity
 */
class UpdateTaskWithOrganization extends UpdateWithOrganization implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [LoadOrganizationAndBusinessUnitData::class];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->update($manager, 'OroTaskBundle:Task');
    }
}
