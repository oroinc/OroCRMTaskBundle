<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_15;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManager;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtension;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Rename columns "createdAt" and "updatedAt" to "created_at" and "updated_at" for the Task entity
 */
class RenameCreatedAtUpdatedAtFields implements Migration, RenameExtensionAwareInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var RenameExtension */
    private $renameExtension;

    /**
     * {@inheritdoc}
     */
    public function setRenameExtension(RenameExtension $renameExtension)
    {
        $this->renameExtension = $renameExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_task');
        $this->renameColumn($schema, $queries, $table, 'createdAt', 'created_at');
        $this->renameColumn($schema, $queries, $table, 'updatedAt', 'updated_at');
    }

    /**
     * @param Schema   $schema
     * @param QueryBag $queries
     * @param Table    $table
     * @param string   $oldColumnName
     * @param string   $newColumnName
     */
    private function renameColumn(Schema $schema, QueryBag $queries, Table $table, $oldColumnName, $newColumnName)
    {
        $this->renameExtension->renameColumn($schema, $queries, $table, $oldColumnName, $newColumnName);

        // keep field name unchanged
        $this->getExtendOptionsManager()->setColumnOptions(
            $table->getName(),
            $oldColumnName,
            [ExtendOptionsManager::NEW_NAME_OPTION => $oldColumnName]
        );
    }

    /**
     * @return ExtendOptionsManager
     */
    private function getExtendOptionsManager()
    {
        return $this->container->get('oro_entity_extend.migration.options_manager');
    }
}
