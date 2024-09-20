<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Stub;

use Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface;
use Oro\Bundle\TaskBundle\Entity\Task;

class TaskStub extends Task
{
    /** @var EnumOptionInterface */
    private $status;

    /**
     * @return EnumOptionInterface
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(EnumOptionInterface $status)
    {
        $this->status = $status;
    }
}
