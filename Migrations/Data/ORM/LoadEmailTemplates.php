<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Oro\Bundle\EmailBundle\Migrations\Data\ORM\AbstractEmailFixture;
use Oro\Bundle\MigrationBundle\Fixture\RenamedFixtureInterface;

/**
 * Loading data for email templates
 */
class LoadEmailTemplates extends AbstractEmailFixture implements RenamedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function getPreviousClassNames(): array
    {
        return [
            'Oro\\Bundle\\ReminderBundle\\Migrations\\Data\\ORM\\LoadEmailTemplates',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailsDir()
    {
        return $this->container
            ->get('kernel')
            ->locateResource('@OroTaskBundle/Migrations/Data/ORM/data/emails');
    }
}
