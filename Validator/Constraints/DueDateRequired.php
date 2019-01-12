<?php

namespace Oro\Bundle\TaskBundle\Validator\Constraints;

use Oro\Bundle\TaskBundle\Validator\DueDateRequiredValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for dueDate field of Task entity
 */
class DueDateRequired extends Constraint
{
    /** @var string */
    public $message = 'oro.task.due_date_required';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return DueDateRequiredValidator::class;
    }
}
