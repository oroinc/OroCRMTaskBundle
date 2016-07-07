<?php

namespace OroCRM\Bundle\TaskBundle\Tests\Unit\Entity;

use OroCRM\Bundle\TaskBundle\Entity\Task;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        new Task();
    }

    public function testSetOwner()
    {
        $entity = new Task();

        $this->assertNull($entity->getOwner());

        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $entity->setOwner($user);

        $this->assertEquals($user, $entity->getOwner());
    }

    public function testGetOwnerId()
    {
        $entity = new Task();

        $this->assertNull($entity->getOwnerId());

        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $expected = 42;
        $user->expects($this->once())->method('getId')->will($this->returnValue($expected));
        $entity->setOwner($user);

        $this->assertEquals($expected, $entity->getOwnerId());
    }

    public function testDueDateExpired()
    {
        $entity = new Task();

        $nowDate = new \DateTime();
        $oneDayInterval = new \DateInterval('P1D');

        $this->assertFalse($entity->isDueDateExpired());

        $dateInPast = $nowDate->sub($oneDayInterval);
        $entity->setDueDate($dateInPast);
        $this->assertTrue($entity->isDueDateExpired());

        $dateInFuture = $nowDate->add($oneDayInterval);
        $entity->setDueDate($dateInFuture);
        $this->assertFalse($entity->isDueDateExpired());
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Task();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        $testTaskPriority = $this->getMockBuilder('OroCRM\Bundle\TaskBundle\Entity\TaskPriority')
            ->disableOriginalConstructor()
            ->getMock();
        
        $organization = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');
        return array(
            array('id', 42),
            array('subject', 'Test subject'),
            array('description', 'Test Description'),
            array('taskPriority', $testTaskPriority),
            array('dueDate', new \DateTime()),
            array('createdAt', new \DateTime()),
            array('updatedAt', new \DateTime()),
            array('organization', $organization, $organization)
        );
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
