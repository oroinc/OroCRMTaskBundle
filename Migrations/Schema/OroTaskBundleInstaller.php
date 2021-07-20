<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\TaskBundle\Migrations\Schema\v1_11_1\AddTaskStatusField;
use Oro\Bundle\TaskBundle\Migrations\Schema\v1_9\AddActivityAssociations;

/**
 * Installer for TaskBundle
 */
class OroTaskBundleInstaller implements
    Installation,
    ActivityExtensionAwareInterface,
    CommentExtensionAwareInterface,
    ExtendExtensionAwareInterface
{
    /** @var ActivityExtension */
    protected $activityExtension;

    /** @var CommentExtension */
    protected $comment;

    /** @var ExtendExtension */
    protected $extendExtension;

    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    public function setCommentExtension(CommentExtension $commentExtension)
    {
        $this->comment = $commentExtension;
    }

    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_15';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createOrocrmTaskTable($schema);
        $this->createOrocrmTaskPriorityTable($schema);

        /** Foreign keys generation **/
        $this->addOrocrmTaskForeignKeys($schema);

        /** Add comment relation */
        $this->comment->addCommentAssociation($schema, 'orocrm_task');

        AddActivityAssociations::addActivityAssociations($schema, $this->activityExtension);
        AddTaskStatusField::addTaskStatusField($schema, $this->extendExtension);
        AddTaskStatusField::addEnumValues($queries, $this->extendExtension);
    }

    protected function createOrocrmTaskTable(Schema $schema)
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
        $table->addIndex(['updated_at', 'id'], 'task_updated_at_idx', []);
    }

    protected function createOrocrmTaskPriorityTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_task_priority');
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('`order`', 'integer', ['notnull' => true]);
        $table->setPrimaryKey(['name']);
        $table->addUniqueIndex(['label'], 'UNIQ_DB8472D3EA750E8');
    }

    protected function addOrocrmTaskForeignKeys(Schema $schema)
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
}
