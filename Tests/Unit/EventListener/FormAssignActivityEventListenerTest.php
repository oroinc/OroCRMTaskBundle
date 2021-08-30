<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\EventListener;

use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\FormBundle\Event\FormHandler\AfterFormProcessEvent;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\EventListener\FormAssignActivityEventListener;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FormAssignActivityEventListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var EntityRoutingHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $entityRoutingHelper;

    /** @var RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    private $requestStack;

    /** @var ActivityManager|\PHPUnit\Framework\MockObject\MockObject */
    private $activityManager;

    /** @var FormAssignActivityEventListener */
    private $listener;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->entityRoutingHelper = $this->createMock(EntityRoutingHelper::class);
        $this->activityManager = $this->createMock(ActivityManager::class);

        $this->listener = new FormAssignActivityEventListener(
            $this->activityManager,
            $this->entityRoutingHelper,
            $this->requestStack
        );
    }

    public function testAssignActivityWithTaskWithContextsField()
    {
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('has')
            ->with('contexts')
            ->willReturn(true);

        $this->requestStack->expects($this->never())
            ->method('getCurrentRequest');

        $task = new Task();
        $event = new AfterFormProcessEvent($form, $task);

        $this->listener->assignActivityWithTask($event);
    }

    public function testAssignActivityWithTaskWithoutRequest()
    {
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('has')
            ->with('contexts')
            ->willReturn(false);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->entityRoutingHelper->expects($this->never())
            ->method('getAction');

        $task = new Task();
        $event = new AfterFormProcessEvent($form, $task);

        $this->listener->assignActivityWithTask($event);
    }

    public function testAssignActivityWithTaskWithNotApplicableAction()
    {
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('has')
            ->with('contexts')
            ->willReturn(false);

        $currentRequest = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn('not-activity');

        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityClassName');

        $task = new Task();
        $event = new AfterFormProcessEvent($form, $task);

        $this->listener->assignActivityWithTask($event);
    }

    public function testAssignActivityWithTaskWithNotApplicableRequestEntityClassName()
    {
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('has')
            ->with('contexts')
            ->willReturn(false);

        $currentRequest = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn(FormAssignActivityEventListener::ACTION_ACTIVITY);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityClassName')
            ->with($currentRequest)
            ->willReturn(null);

        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityId');

        $task = new Task();
        $event = new AfterFormProcessEvent($form, $task);

        $this->listener->assignActivityWithTask($event);
    }

    public function testAssignActivityWithTaskWithNotApplicableRequestEntityId()
    {
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('has')
            ->with('contexts')
            ->willReturn(false);

        $currentRequest = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn(FormAssignActivityEventListener::ACTION_ACTIVITY);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityClassName')
            ->with($currentRequest)
            ->willReturn(User::class);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityId')
            ->willReturn(null);

        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityReference');

        $task = new Task();
        $event = new AfterFormProcessEvent($form, $task);

        $this->listener->assignActivityWithTask($event);
    }

    public function testAssignActivityWithTask()
    {
        $task = new Task();
        $currentRequest = $this->createMock(Request::class);
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn(FormAssignActivityEventListener::ACTION_ACTIVITY);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityClassName')
            ->with($currentRequest)
            ->willReturn(User::class);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityId')
            ->with($currentRequest)
            ->willReturn(1);

        $entityReference = new User();
        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityReference')
            ->with(User::class, 1)
            ->willReturn($entityReference);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('has')
            ->with('contexts')
            ->willReturn(false);

        $this->activityManager->expects($this->once())
            ->method('addActivityTarget')
            ->with($task, $entityReference);

        $event = new AfterFormProcessEvent($form, $task);

        $this->listener->assignActivityWithTask($event);
    }
}
