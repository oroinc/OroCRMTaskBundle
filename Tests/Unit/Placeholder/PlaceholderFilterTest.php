<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Placeholder;

use Oro\Bundle\CalendarBundle\Entity\Calendar;
use Oro\Bundle\TaskBundle\Placeholder\PlaceholderFilter;
use PHPUnit\Framework\TestCase;

class PlaceholderFilterTest extends TestCase
{
    public function testIsCalendarTasksVisibleWithoutEnabledMyTasks(): void
    {
        $placeholder = new PlaceholderFilter(false);
        self::assertFalse($placeholder->isCalendarTasksVisible(new \stdClass()));
    }

    public function testIsCalendarTasksVisibleWithEnabledMyTasksWrongObject(): void
    {
        $placeholder = new PlaceholderFilter(true);
        self::assertFalse($placeholder->isCalendarTasksVisible(new \stdClass()));
    }

    public function testIsCalendarTasksVisibleWithEnabledMyTasks(): void
    {
        $placeholder = new PlaceholderFilter(true);
        self::assertTrue($placeholder->isCalendarTasksVisible(new Calendar()));
    }
}
