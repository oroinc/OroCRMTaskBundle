<?php

namespace Oro\Bundle\TaskBundle\Model;

use Oro\Bundle\ActivityBundle\Model\ActivityInterface;
use Oro\Bundle\ActivityBundle\Model\ExtendActivity;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\TaskBundle\Entity\Task;

/**
 * Extend class which allow to make Task entity extandable
 *
 * @method AbstractEnumValue getStatus()
 * @method Task setStatus(AbstractEnumValue $status)
 */
class ExtendTask implements ActivityInterface
{
    use ExtendActivity;

    /**
     * Constructor
     *
     * The real implementation of this method is auto generated.
     *
     * IMPORTANT: If the derived class has own constructor it must call parent constructor.
     */
    public function __construct()
    {
    }
}
