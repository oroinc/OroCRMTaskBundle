<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Validator\Constraints;

use Oro\Bundle\TaskBundle\Validator\Constraints\DueDateRequired;
use Symfony\Component\Validator\Constraint;

class DueDateRequiredTest extends \PHPUnit\Framework\TestCase
{
    /** @var DueDateRequired */
    protected $constraint;

    protected function setUp(): void
    {
        $this->constraint = new DueDateRequired();
    }

    protected function tearDown(): void
    {
        unset($this->constraint);
    }

    public function testConfiguration()
    {
        self::assertEquals(
            'Oro\Bundle\TaskBundle\Validator\DueDateRequiredValidator',
            $this->constraint->validatedBy()
        );
        self::assertEquals('oro.task.due_date_required', $this->constraint->message);
        self::assertEquals(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }
}
