<?php

namespace Oro\Bundle\TaskBundle\Validator\Constraints;

use Oro\Bundle\TaskBundle\Entity\Task;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that field "dueDate" mus be set in case of number of reminders more than one
 */
class DueDateRequiredValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DueDateRequired) {
            throw new UnexpectedTypeException($constraint, DueDateRequired::class);
        }
        if (!$value instanceof Task) {
            throw new UnexpectedTypeException($value, Task::class);
        }

        if (count($value->getReminders()) > 0 && !$value->getDueDate()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('dueDate')
                ->setParameters(['{{ field }}'  => 'reminders'])
                ->addViolation();
        }
    }
}
