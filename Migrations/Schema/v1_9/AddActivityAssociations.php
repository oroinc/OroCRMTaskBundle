<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_9;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddActivityAssociations implements Migration, ActivityExtensionAwareInterface
{
    use ActivityExtensionAwareTrait;

    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->activityExtension->addActivityAssociation($schema, 'orocrm_task', 'oro_email');
        $this->activityExtension->addActivityAssociation($schema, 'oro_email', 'orocrm_task');
    }
}
