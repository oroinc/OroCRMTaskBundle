<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Oro\Bundle\EmailBundle\Migrations\Data\ORM\AbstractEmailFixture;
use Oro\Bundle\MigrationBundle\Fixture\RenamedFixtureInterface;

/**
 * Loads email templates.
 */
class LoadEmailTemplates extends AbstractEmailFixture implements RenamedFixtureInterface
{
    #[\Override]
    public function getPreviousClassNames(): array
    {
        return [
            'Oro\\Bundle\\ReminderBundle\\Migrations\\Data\\ORM\\LoadEmailTemplates',
        ];
    }

    #[\Override]
    public function getEmailsDir(): string
    {
        return $this->container
            ->get('kernel')
            ->locateResource('@OroTaskBundle/Migrations/Data/ORM/data/emails');
    }
}
