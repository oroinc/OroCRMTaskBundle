<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Entity;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Component\Testing\Unit\EntityTrait;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    use EntityTestCaseTrait;
    use EntityTrait;

    public function testGetOwnerId()
    {
        $entity = new Task();

        $this->assertNull($entity->getOwnerId());

        $user = $this->createMock(User::class);
        $expected = 42;
        $user->expects($this->once())->method('getId')->will($this->returnValue($expected));
        $entity->setOwner($user);

        $this->assertEquals($expected, $entity->getOwnerId());
    }

    public function testDueDateExpired()
    {
        $entity = new Task();

        $oneDayInterval = new \DateInterval('P1D');

        $dateInPast = new \DateTime();
        $dateInPast->sub($oneDayInterval);
        $dateInFuture = new \DateTime();
        $dateInFuture->add($oneDayInterval);

        $this->assertFalse($entity->isDueDateExpired());

        $entity->setDueDate($dateInPast);
        $this->assertTrue($entity->isDueDateExpired());

        $entity->setDueDate($dateInFuture);
        $this->assertFalse($entity->isDueDateExpired());
    }

    public function testProperties()
    {
        $taskPriority = $this->getEntity(TaskPriority::class);
        $organization = $this->getEntity(Organization::class);
        $user = $this->getEntity(User::class);

        $now = new \DateTime('now');

        $properties = [
            ['id', 42],
            ['subject', 'Test subject'],
            ['description', 'Test Description'],
            ['taskPriority', $taskPriority],
            ['dueDate', $now, false],
            ['createdAt', $now, false],
            ['updatedAt', $now, false],
            ['organization', $organization],
            ['createdBy', $user],
        ];
    }

    public function testIsUpdatedFlags()
    {
        $date = new \DateTime('2012-12-12 12:12:12');
        $task = new Task();
        $task->setUpdatedAt($date);

        $this->assertTrue($task->isUpdatedAtSet());
    }

    public function testIsNotUpdatedFlags()
    {
        $task = new Task();
        $task->setUpdatedAt(null);

        $this->assertFalse($task->isUpdatedAtSet());
    }
}
