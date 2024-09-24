<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroTaskBundle implements Migration
{
    #[\Override]
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_task');
        $table->changeColumn('due_date', ['notnull' => false]);
    }
}
