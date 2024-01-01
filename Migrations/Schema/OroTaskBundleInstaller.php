<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareTrait;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareTrait;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareTrait;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\EntityExtendBundle\Migration\Query\EnumDataValue;
use Oro\Bundle\EntityExtendBundle\Migration\Query\InsertEnumValuesQuery;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroTaskBundleInstaller implements
    Installation,
    ActivityExtensionAwareInterface,
    CommentExtensionAwareInterface,
    ExtendExtensionAwareInterface
{
    use ActivityExtensionAwareTrait;
    use CommentExtensionAwareTrait;
    use ExtendExtensionAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function getMigrationVersion(): string
    {
        return 'v1_15';
    }

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        /** Tables generation **/
        $this->createOrocrmTaskTable($schema);
        $this->createOrocrmTaskPriorityTable($schema);

        /** Foreign keys generation **/
        $this->addOrocrmTaskForeignKeys($schema);

        $this->commentExtension->addCommentAssociation($schema, 'orocrm_task');
        $this->activityExtension->addActivityAssociation($schema, 'orocrm_task', 'oro_email');
        $this->activityExtension->addActivityAssociation($schema, 'oro_email', 'orocrm_task');

        $this->addTaskStatusField($schema, $queries);
    }

    private function createOrocrmTaskTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_task');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('subject', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('due_date', 'datetime', ['notnull' => false]);
        $table->addColumn('task_priority_name', 'string', ['notnull' => false, 'length' => 32]);
        $table->addColumn('created_by_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['due_date', 'id'], 'task_due_date_idx');
        $table->addIndex(['updated_at', 'id'], 'task_updated_at_idx');
    }

    private function createOrocrmTaskPriorityTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_task_priority');
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('`order`', 'integer', ['notnull' => true]);
        $table->setPrimaryKey(['name']);
        $table->addUniqueIndex(['label'], 'UNIQ_DB8472D3EA750E8');
    }

    private function addOrocrmTaskForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('orocrm_task');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_task_priority'),
            ['task_priority_name'],
            ['name'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['created_by_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
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
