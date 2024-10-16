<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Oro\Bundle\EntityExtendBundle\Migration\Fixture\AbstractEnumFixture;

/**
 * Load task satus enum options data.
 */
class LoadTaskStatusOptionData extends AbstractEnumFixture
{
    #[\Override]
    protected function getData(): array
    {
        return [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'closed' => 'Closed',
        ];
    }

    #[\Override]
    protected function getDefaultValue(): string
    {
        return 'open';
    }

    #[\Override]
    protected function getEnumCode(): string
    {
        return 'task_status';
    }
}
