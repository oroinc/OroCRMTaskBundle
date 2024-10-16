<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_15;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManager;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManagerAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManagerAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtensionAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Rename columns "createdAt" and "updatedAt" to "created_at" and "updated_at" for the Task entity
 */
class RenameCreatedAtUpdatedAtFields implements
    Migration,
    RenameExtensionAwareInterface,
    ExtendOptionsManagerAwareInterface
{
    use RenameExtensionAwareTrait;
    use ExtendOptionsManagerAwareTrait;

    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        $table = $schema->getTable('orocrm_task');
        $this->renameColumn($schema, $queries, $table, 'createdAt', 'created_at');
        $this->renameColumn($schema, $queries, $table, 'updatedAt', 'updated_at');
    }

    private function renameColumn(
        Schema $schema,
        QueryBag $queries,
        Table $table,
        string $oldColumnName,
        string $newColumnName
    ): void {
        $this->renameExtension->renameColumn($schema, $queries, $table, $oldColumnName, $newColumnName);

        // keep field name unchanged
        $this->extendOptionsManager->setColumnOptions(
            $table->getName(),
            $oldColumnName,
            [ExtendOptionsManager::NEW_NAME_OPTION => $oldColumnName]
        );
    }
}
