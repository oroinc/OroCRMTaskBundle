<?php

namespace Oro\Bundle\TaskBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroTaskBundle implements Migration, CommentExtensionAwareInterface
{
    use CommentExtensionAwareTrait;

    #[\Override]
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->commentExtension->addCommentAssociation($schema, 'orocrm_task');
    }
}
