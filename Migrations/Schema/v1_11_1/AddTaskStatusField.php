<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_11_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigEntityValueQuery;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareTrait;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\EntityExtendBundle\Migration\Query\EnumDataValue;
use Oro\Bundle\EntityExtendBundle\Migration\Query\InsertEnumValuesQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddTaskStatusField implements Migration, ExtendExtensionAwareInterface
{
    use ExtendExtensionAwareTrait;

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
        $queries->addPostQuery(new UpdateTaskStatusQuery($this->extendExtension));
        $queries->addQuery(new UpdateEntityConfigEntityValueQuery(
            'Oro\Bundle\TaskBundle\Entity\Task',
            'workflow',
            'show_step_in_grid',
            false
        ));
    }

    private function addTaskStatusField(Schema $schema, QueryBag $queries): void
    {
        $enumTable = $this->extendExtension->addEnumField(
            $schema,
            'orocrm_task',
            'status',
            'task_status'
        );

        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', ['open', 'in_progress', 'closed']);
        $enumTable->addOption(OroOptions::KEY, $options);

        $queries->addPostQuery(new InsertEnumValuesQuery($this->extendExtension, 'task_status', [
            new EnumDataValue('open', 'Open', 1, true),
            new EnumDataValue('in_progress', 'In Progress', 2),
            new EnumDataValue('closed', 'Closed', 3)
        ]));
    }
}
