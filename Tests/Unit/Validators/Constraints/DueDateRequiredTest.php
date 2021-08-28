<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Validator\Constraints;

use Oro\Bundle\TaskBundle\Validator\Constraints\DueDateRequired;
use Oro\Bundle\TaskBundle\Validator\DueDateRequiredValidator;
use Symfony\Component\Validator\Constraint;

class DueDateRequiredTest extends \PHPUnit\Framework\TestCase
{
    /** @var DueDateRequired */
    private $constraint;

    protected function setUp(): void
    {
        $this->constraint = new DueDateRequired();
    }

    public function testConfiguration()
    {
        self::assertEquals(
            DueDateRequiredValidator::class,
            $this->constraint->validatedBy()
        );
        self::assertEquals('oro.task.due_date_required', $this->constraint->message);
        self::assertEquals(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }
}
