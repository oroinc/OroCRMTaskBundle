<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_11_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigEntityValueQuery;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareTrait;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddTaskStatusField implements Migration, ExtendExtensionAwareInterface
{
    use ExtendExtensionAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_task');
        if ($table->hasColumn('status_id')) {
            return;
        }

        static::addTaskStatusField($schema, $this->extendExtension);
        static::addEnumValues($queries, $this->extendExtension);

        $queries->addPostQuery(new UpdateTaskStatusQuery($this->extendExtension));

        $queries->addQuery(
            new UpdateEntityConfigEntityValueQuery(
                'Oro\Bundle\TaskBundle\Entity\Task',
                'workflow',
                'show_step_in_grid',
                false
            )
        );
    }

    public static function addTaskStatusField(Schema $schema, ExtendExtension $extendExtension)
    {
        $enumTable = $extendExtension->addEnumField(
            $schema,
            'orocrm_task',
            'status',
            'task_status'
        );

        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', ['open', 'in_progress', 'closed']);

        $enumTable->addOption(OroOptions::KEY, $options);
    }

    public static function addEnumValues(QueryBag $queries, ExtendExtension $extendExtension)
    {
        $queries->addPostQuery(new InsertTaskStatusesQuery($extendExtension));
    }
}
