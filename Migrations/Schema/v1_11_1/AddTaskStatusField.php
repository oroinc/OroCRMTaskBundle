<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_11_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigEntityValueQuery;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\OutdatedExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\OutdatedExtendExtensionAwareTrait;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\EntityExtendBundle\Migration\Query\OutdatedEnumDataValue;
use Oro\Bundle\EntityExtendBundle\Migration\Query\OutdatedInsertEnumValuesQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddTaskStatusField implements Migration, OutdatedExtendExtensionAwareInterface
{
    use OutdatedExtendExtensionAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        $table = $schema->getTable('orocrm_task');
        if ($table->hasColumn('status_id')) {
            return;
        }

        $this->addTaskStatusField($schema, $queries);
        $queries->addPostQuery(new UpdateTaskStatusQuery($this->outdatedExtendExtension));
        $queries->addQuery(new UpdateEntityConfigEntityValueQuery(
            'Oro\Bundle\TaskBundle\Entity\Task',
            'workflow',
            'show_step_in_grid',
            false
        ));
    }

    private function addTaskStatusField(Schema $schema, QueryBag $queries): void
    {
        $enumTable = $this->outdatedExtendExtension->addOutdatedEnumField(
            $schema,
            'orocrm_task',
            'status',
            'task_status'
        );

        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', ['open', 'in_progress', 'closed']);
        $enumTable->addOption(OroOptions::KEY, $options);

        $queries->addPostQuery(new OutdatedInsertEnumValuesQuery($this->outdatedExtendExtension, 'task_status', [
            new OutdatedEnumDataValue('open', 'Open', 1, true),
            new OutdatedEnumDataValue('in_progress', 'In Progress', 2),
            new OutdatedEnumDataValue('closed', 'Closed', 3)
        ]));
    }
}
