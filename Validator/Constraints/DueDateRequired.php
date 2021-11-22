<?php

namespace Oro\Bundle\TaskBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for dueDate field of Task entity
 */
class DueDateRequired extends Constraint
{
    public string $message = 'oro.task.due_date_required';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
