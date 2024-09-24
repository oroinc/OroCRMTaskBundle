<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroTaskBundle implements Migration
{
    #[\Override]
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_task');
        $table->addIndex(['updatedAt'], 'task_updated_at_idx', []);
    }
}
