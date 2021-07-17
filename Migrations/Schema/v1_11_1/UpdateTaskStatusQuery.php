<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_11_1;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\MigrationBundle\Migration\ArrayLogger;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;
use Oro\Bundle\TaskBundle\Entity\Task;
use Psr\Log\LoggerInterface;

class UpdateTaskStatusQuery extends ParametrizedMigrationQuery
{
    /** @var $extendExtension */
    protected $extendExtension;

    public function __construct(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        $logger = new ArrayLogger();
        $logger->info(
            'Update task status field from workflow step.'
        );
        $this->doExecute($logger, true);

        return $logger->getMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(LoggerInterface $logger)
    {
        $this->doExecute($logger);
    }

    /**
     * @param LoggerInterface $logger
     * @param bool            $dryRun
     */
    public function doExecute(LoggerInterface $logger, $dryRun = false)
    {
        $platform = $this->connection->getDatabasePlatform();
        if ($platform instanceof PostgreSqlPlatform) {
            $this->updatePostgres($logger, $dryRun);
        }

        if ($platform instanceof MySqlPlatform) {
            $this->updateMysql($logger, $dryRun);
        }

        $this->updateDefaultTaskStatus($logger, $dryRun, 'open');
    }

    /**
     * @param LoggerInterface $logger
     * @param bool            $dryRun
     */
    protected function updatePostgres(LoggerInterface $logger, $dryRun)
    {
        $tableName = $this->extendExtension->getNameGenerator()->generateEnumTableName('task_status');

        $sql = 'UPDATE orocrm_task t' .
           ' SET status_id = ts.id' .
           ' FROM %s ts' .
           ' JOIN oro_workflow_item wi ON wi.entity_class = :entity_class AND wi.workflow_name = :workflow_name' .
           ' JOIN oro_workflow_step ws ON ws.id = wi.current_step_id' .
           ' WHERE ts.id = ws.name AND CAST(wi.entity_id as TEXT) = CAST(t.id as TEXT)';
        $sql = sprintf($sql, $tableName);

        $params = [
            'entity_class' => Task::class,
            'workflow_name' => 'task_flow'
        ];
        $types  = [
            'entity_class' => 'string',
            'workflow_name' => 'string'
        ];

        $this->logQuery($logger, $sql, $params, $types);
        if (!$dryRun) {
            $this->connection->executeStatement($sql, $params, $types);
        }
    }

    /**
     * @param LoggerInterface $logger
     * @param bool            $dryRun
     */
    protected function updateMysql(LoggerInterface $logger, $dryRun)
    {
        $tableName = $this->extendExtension->getNameGenerator()->generateEnumTableName('task_status');

        $sql = 'UPDATE orocrm_task t' .
            ' JOIN oro_workflow_item wi ON wi.entity_class = :entity_class AND wi.workflow_name = :workflow_name' .
            ' JOIN oro_workflow_step ws ON ws.id = wi.current_step_id' .
            ' JOIN %s ts ON ts.id = ws.name' .
            ' SET status_id = ts.id' .
            ' WHERE CAST(wi.entity_id as CHAR) = CAST(t.id as CHAR)';
        $sql = sprintf($sql, $tableName);

        $params = [
            'entity_class' => Task::class,
            'workflow_name' => 'task_flow'
        ];
        $types  = [
            'entity_class' => 'string',
            'workflow_name' => 'string'
        ];

        $this->logQuery($logger, $sql, $params, $types);
        if (!$dryRun) {
            $this->connection->executeStatement($sql, $params, $types);
        }
    }

    /**
     * Set task status to open on tasks that had no assigned workflow steps
     *
     * @param LoggerInterface $logger
     * @param bool            $dryRun
     * @param string          $defaultStatus
     */
    protected function updateDefaultTaskStatus(LoggerInterface $logger, $dryRun, $defaultStatus)
    {
        $sql    = 'UPDATE orocrm_task SET status_id = :status_id WHERE status_id IS NULL';
        $params = ['status_id' => $defaultStatus];
        $types  = ['status_id' => 'string'];

        $this->logQuery($logger, $sql, $params, $types);
        if (!$dryRun) {
            $this->connection->executeStatement($sql, $params, $types);
        }
    }
}
