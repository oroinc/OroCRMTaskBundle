<?php

namespace Oro\Bundle\TaskBundle\Validator;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Validator\Constraints\DueDateRequired;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that field "dueDate" mus be set in case of number of reminders more than one
 */
class DueDateRequiredValidator extends ConstraintValidator
{
    /**
     * @param Task                       $value
     * @param Constraint|DueDateRequired $constraint
     * @throws \InvalidArgumentException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Task) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Oro\Bundle\TaskBundle\Entity\Task supported only, %s given',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if (count($value->getReminders()) > 0 && !$value->getDueDate()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('dueDate')
                ->setParameters(['{{ field }}'  => 'reminders'])
                ->addViolation();
        }
    }
}
