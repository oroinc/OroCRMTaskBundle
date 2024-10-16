<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class CreateActivityAssociation implements Migration, OrderedMigrationInterface, ActivityExtensionAwareInterface
{
    use ActivityExtensionAwareTrait;

    #[\Override]
    public function getOrder()
    {
        return 1;
    }

    #[\Override]
    public function up(Schema $schema, QueryBag $queries)
    {
        if ($schema->hasTable('orocrm_account')) {
            $this->activityExtension->addActivityAssociation($schema, 'orocrm_task', 'orocrm_account');
        }
        if ($schema->hasTable('orocrm_contact')) {
            $this->activityExtension->addActivityAssociation($schema, 'orocrm_task', 'orocrm_contact');
        }
    }
}
