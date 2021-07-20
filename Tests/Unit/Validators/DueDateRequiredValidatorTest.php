<?php

namespace Oro\Bundle\EmailBundle\Tests\Unit\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\ReminderBundle\Entity\Reminder;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Validator\Constraints\DueDateRequired;
use Oro\Bundle\TaskBundle\Validator\DueDateRequiredValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class DueDateRequiredValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DueDateRequiredValidator
     */
    protected $validator;

    /**
     * @var DueDateRequired
     */
    protected $constraint;

    protected function setUp(): void
    {
        $this->validator = new DueDateRequiredValidator();
        $this->constraint = $this->createMock(DueDateRequired::class);
    }

    /**
     * @dataProvider invalidArgumentProvider
     *
     * @param mixed $value
     * @param string $expectedExceptionMessage
     */
    public function testInvalidArgument($value, string $expectedExceptionMessage)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->validator->validate($value, $this->constraint);
    }

    /**
     * @return array
     */
    public function invalidArgumentProvider()
    {
        return [
            'bool' => [
                'value' => true,
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, boolean given',
            ],
            'string' => [
                'value' => 'string',
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, string given',
            ],
            'integer' => [
                'value' => 5,
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, integer given',
            ],
            'null' => [
                'value' => null,
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, NULL given',
            ],
            'object' => [
                'value' => new \stdClass(),
                'expectedExceptionMessage' =>
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, stdClass given',
            ],
            'array' => [
                'value' => [],
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
        $context = $this->createMock(ExecutionContext::class);

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($builder);
        $builder->expects($this->once())
            ->method('atPath')
            ->willReturnSelf();
        $builder->expects($this->once())
            ->method('setParameters')
            ->willReturnSelf();
        $builder->expects($this->once())
            ->method('addViolation');

        $this->validator->initialize($context);

        $this->validator->validate($entity, $this->constraint);
    }

    /**
     * @return array
     */
    public function validateNotValidProvider()
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
        $context = $this->createMock(ExecutionContext::class);

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $context->expects($this->never())
            ->method('buildViolation')
            ->willReturn($builder);
        $builder->expects($this->never())
            ->method('atPath')
            ->willReturnSelf();
        $builder->expects($this->never())
            ->method('setParameters')
            ->willReturnSelf();
        $builder->expects($this->never())
            ->method('addViolation');

        $this->validator->initialize($context);

        $this->validator->validate($entity, $this->constraint);
    }

    /**
     * @return array
     */
    public function validateValidProvider()
    {
        return [
            'setDateAndReminders' => [
                'entityData' => $this->createTask(new \DateTime(), 2),
            ],
            'setDateNoReminders' => [
                'entityData' => $this->createTask(new \DateTime(), 0),
            ],
            'noDateWithoutReminders' => [
                'entityData' => $this->createTask(null, 0),
            ],
        ];
    }

    /**
     * @param \DateTime $dueDate
     * @param int $remindersCount
     * @return Task
     */
    private function createTask($dueDate, $remindersCount)
    {
        $task = new Task();
        $task->setSubject(uniqid('subject'));
        $task->setDueDate($dueDate);
        $reminders = new ArrayCollection();
        for ($i = 0; $i < $remindersCount; $i++) {
            $reminders->add(new Reminder());
        }
        $task->setReminders($reminders);

        return $task;
    }
}
