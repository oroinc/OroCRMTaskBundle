<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\ReminderBundle\Entity\Reminder;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Validator\Constraints\DueDateRequired;
use Oro\Bundle\TaskBundle\Validator\DueDateRequiredValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class DueDateRequiredValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new DueDateRequiredValidator();
    }

    private function createTask(?\DateTime $dueDate, int $remindersCount): Task
    {
        $task = new Task();
        $task->setSubject('Test Task');
        $task->setDueDate($dueDate);
        $reminders = new ArrayCollection();
        for ($i = 0; $i < $remindersCount; $i++) {
            $reminders->add(new Reminder());
        }
        $task->setReminders($reminders);

        return $task;
    }

    /**
     * @dataProvider invalidArgumentProvider
     */
    public function testInvalidArgument($value, string $expectedExceptionMessage)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $constraint = new DueDateRequired();
        $this->validator->validate($value, $constraint);
    }

    public function invalidArgumentProvider(): array
    {
        return [
            'bool'    => [
                'value'                    => true,
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, boolean given',
            ],
            'string'  => [
                'value'                    => 'string',
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, string given',
            ],
            'integer' => [
                'value'                    => 5,
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, integer given',
            ],
            'null'    => [
                'value'                    => null,
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, NULL given',
            ],
            'object'  => [
                'value'                    => new \stdClass(),
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, stdClass given',
            ],
            'array'   => [
                'value'                    => [],
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, array given',
            ],
        ];
    }

    /**
     * @dataProvider validateNotValidProvider
     */
    public function testValidateNotValid(Task $entity)
    {
        $constraint = new DueDateRequired();
        $this->validator->validate($entity, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ field }}', 'reminders')
            ->atPath('property.path.dueDate')
            ->assertRaised();
    }

    public function validateNotValidProvider(): array
    {
        return [
            'noDateWithReminders' => [
                'entityData' => $this->createTask(null, 1),
            ],
        ];
    }

    /**
     * @dataProvider validateValidProvider
     */
    public function testValidateValid(Task $entity)
    {
        $constraint = new DueDateRequired();
        $this->validator->validate($entity, $constraint);

        $this->assertNoViolation();
    }


    public function validateValidProvider(): array
    {
        return [
            'setDateAndReminders'    => [
                'entityData' => $this->createTask(new \DateTime(), 2),
            ],
            'setDateNoReminders'     => [
                'entityData' => $this->createTask(new \DateTime(), 0),
            ],
            'noDateWithoutReminders' => [
                'entityData' => $this->createTask(null, 0),
            ],
        ];
    }
}
