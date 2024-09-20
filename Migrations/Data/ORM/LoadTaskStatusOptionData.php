<?php

namespace Oro\Bundle\TaskBundle\Migrations\Data\ORM;

use Oro\Bundle\EntityExtendBundle\Migration\Fixture\AbstractEnumFixture;

/**
 * Load task satus enum options data.
 */
class LoadTaskStatusOptionData extends AbstractEnumFixture
{
    protected function getData(): array
    {
        return [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'closed' => 'Closed',
        ];
    }

    protected function getDefaultValue(): string
    {
        return 'open';
    }

    protected function getEnumCode(): string
    {
        return 'task_status';
    }
}
