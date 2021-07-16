<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Stub;

use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\TaskBundle\Entity\Task;

class TaskStub extends Task
{
    /** @var AbstractEnumValue */
    private $status;

    /**
     * @return AbstractEnumValue
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(AbstractEnumValue $status)
    {
        $this->status = $status;
    }
}
