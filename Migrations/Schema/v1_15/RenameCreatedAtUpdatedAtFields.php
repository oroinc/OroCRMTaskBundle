<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_15;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtension;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Rename columns "createdAt" and "updatedAt" to "created_at" and "updated_at" for the Task entity
 */
class RenameCreatedAtUpdatedAtFields implements Migration, RenameExtensionAwareInterface
{
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
        $this->renameExtension->renameColumn($schema, $queries, $table, 'createdAt', 'created_at');
        $this->renameExtension->renameColumn($schema, $queries, $table, 'updatedAt', 'updated_at');
    }
}
