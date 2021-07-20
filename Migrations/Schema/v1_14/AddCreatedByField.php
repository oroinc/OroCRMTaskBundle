<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_14;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\EntityBundle\ORM\DatabasePlatformInterface;
use Oro\Bundle\MigrationBundle\Migration\ConnectionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\TaskBundle\Entity\Task;

/**
 * Add 'createdBy' field to the Task entity
 */
class AddCreatedByField implements Migration, ConnectionAwareInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_task');
        $table->addColumn('created_by_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['created_by_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );

        $platformName = $this->connection->getDatabasePlatform()->getName();
        if ($platformName === DatabasePlatformInterface::DATABASE_POSTGRESQL) {
            $updateQuery = 'UPDATE orocrm_task t '.
                'SET created_by_id = a.user_id '.
                'FROM oro_audit a '.
                'WHERE a.object_id = CAST(t.id as TEXT) AND a.object_class = :className AND a.action = \'create\'';
        } elseif ($platformName === DatabasePlatformInterface::DATABASE_MYSQL) {
            $updateQuery = 'UPDATE orocrm_task t '.
                'INNER JOIN oro_audit a ON a.object_id = t.id AND a.object_class = :className AND a.action = "create" '.
                'SET t.created_by_id = a.user_id;';
        } else {
            return;
        }

        $queries->addQuery(
            new ParametrizedSqlMigrationQuery(
                $updateQuery,
                ['className' => Task::class],
                ['className' => Types::STRING]
            )
        );
    }
}
