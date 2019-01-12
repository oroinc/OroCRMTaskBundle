<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Placeholder;

use Oro\Bundle\CalendarBundle\Entity\Calendar;
use Oro\Bundle\TaskBundle\Placeholder\PlaceholderFilter;
use PHPUnit\Framework\TestCase;

class PlaceholderFilterTest extends TestCase
{
    public function testIsCalendarTasksVisibleWithoutEnabledMyTasks()
    {
        $placeholder = new PlaceholderFilter(false);
        self::assertFalse($placeholder->isCalendarTasksVisible(new \stdClass));
    }

    public function testIsCalendarTasksVisibleWithEnabledMyTasksWrongObject()
    {
        $placeholder = new PlaceholderFilter(true);
        self::assertFalse($placeholder->isCalendarTasksVisible(new \stdClass));
    }

    public function testIsCalendarTasksVisibleWithEnabledMyTasks()
    {
        $placeholder = new PlaceholderFilter(true);
        self::assertTrue($placeholder->isCalendarTasksVisible(new Calendar()));
    }
}
