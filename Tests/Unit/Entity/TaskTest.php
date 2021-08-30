<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\ReminderBundle\Entity\Reminder;
use Oro\Bundle\ReminderBundle\Model\ReminderData;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;

class TaskTest extends \PHPUnit\Framework\TestCase
{
    use EntityTestCaseTrait;

    public function testProperties()
    {
        $properties = [
            ['id', 1],
            ['subject', 'Test subject'],
            ['description', 'Test Description'],
            ['dueDate', new \DateTime()],
            ['taskPriority', new TaskPriority('low')],
            ['owner', new User()],
            ['createdBy', new User()],
            ['organization', new Organization()],
            ['createdAt', new \DateTime()],
            ['updatedAt', new \DateTime()],
        ];

        self::assertPropertyAccessors(new Task(), $properties);
    }

    public function testCollections()
    {
        $collections = [
            ['reminders', new Reminder()],
        ];

        self::assertPropertyCollections(new Task(), $collections);
    }

    public function testSetReminders()
    {
        $task = new Task();
        self::assertInstanceOf(Collection::class, $task->getReminders());
        self::assertEmpty($task->getReminders());

        $task->setReminders(new ArrayCollection([new Reminder(), new Reminder()]));

        self::assertInstanceOf(Collection::class, $task->getReminders());
        self::assertCount(2, $task->getReminders());
    }

    public function testDueDateExpired()
    {
        $entity = new Task();

        $oneDayInterval = new \DateInterval('P1D');

        $dateInPast = new \DateTime();
        $dateInPast->sub($oneDayInterval);
        $dateInFuture = new \DateTime();
        $dateInFuture->add($oneDayInterval);

        self::assertFalse($entity->isDueDateExpired());

        $entity->setDueDate($dateInPast);
        self::assertTrue($entity->isDueDateExpired());

        $entity->setDueDate($dateInFuture);
        self::assertFalse($entity->isDueDateExpired());
    }

    public function testGetReminderData()
    {
        $entity = new Task();
        $entity->setSubject('Task subject');
        $entity->setDueDate(new \DateTime());
        $entity->setOwner(new User());

        $reminderDataExpected = new ReminderData();
        $reminderDataExpected->setSubject($entity->getSubject());
        $reminderDataExpected->setExpireAt($entity->getDueDate());
        $reminderDataExpected->setRecipient($entity->getOwner());

        self::assertEquals($reminderDataExpected, $entity->getReminderData());
    }

    public function testGetOwnerId()
    {
        $entity = new Task();
        self::assertEquals(null, $entity->getOwnerId());
        $entity->setOwner(new User());
        self::assertEquals($entity->getOwner()->getId(), $entity->getOwnerId());
    }

    public function testToString()
    {
        $expected = 'Task subject';
        $entity = new Task();
        $entity->setSubject($expected);

        self::assertEquals($expected, (string)$entity);
    }
}
