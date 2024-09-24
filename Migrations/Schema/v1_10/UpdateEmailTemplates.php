<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_10;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class UpdateEmailTemplates implements Migration
{
    #[\Override]
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addQuery(new UpdateReminderEmailTemplates());
    }
}
