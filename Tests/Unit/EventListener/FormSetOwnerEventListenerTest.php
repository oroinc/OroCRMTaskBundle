<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\EventListener;

use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\FormBundle\Event\FormHandler\FormProcessEvent;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\EventListener\FormSetOwnerEventListener;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Form\Type\UserAclSelectType;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FormSetOwnerEventListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var EntityRoutingHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $entityRoutingHelper;

    /** @var RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    private $requestStack;

    /** @var FormSetOwnerEventListener */
    private $listener;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->entityRoutingHelper = $this->createMock(EntityRoutingHelper::class);

        $this->listener = new FormSetOwnerEventListener(
            $this->entityRoutingHelper,
            $this->requestStack
        );
    }

    public function testSetOwnerAndLockFormWithoutRequest()
    {
        $form = $this->createMock(FormInterface::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->entityRoutingHelper->expects($this->never())
            ->method('getAction');

        $task = new Task();
        $event = new FormProcessEvent($form, $task);

        $this->listener->setOwnerAndLockForm($event);
    }

    public function testSetOwnerAndLockFormWithNotApplicableAction()
    {
        $form = $this->createMock(FormInterface::class);
        $currentRequest = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn('not-assign');

        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityClassName');

        $task = new Task();
        $event = new FormProcessEvent($form, $task);

        $this->listener->setOwnerAndLockForm($event);
    }

    public function testSetOwnerAndLockFormWithNotApplicableRequestEntityClassName1()
    {
        $form = $this->createMock(FormInterface::class);
        $currentRequest = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn(FormSetOwnerEventListener::ACTION_ASSIGN);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityClassName')
            ->with($currentRequest)
            ->willReturn(null);

        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityId');

        $task = new Task();
        $event = new FormProcessEvent($form, $task);

        $this->listener->setOwnerAndLockForm($event);
    }

    public function testSetOwnerAndLockFormWithNotApplicableRequestEntityClassName2()
    {
        $form = $this->createMock(FormInterface::class);
        $currentRequest = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn(FormSetOwnerEventListener::ACTION_ASSIGN);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityClassName')
            ->with($currentRequest)
            ->willReturn(\stdClass::class);

        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityId');

        $task = new Task();
        $event = new FormProcessEvent($form, $task);

        $this->listener->setOwnerAndLockForm($event);
    }

    public function testSetOwnerAndLockFormWithNotApplicableRequestEntityId()
    {
        $form = $this->createMock(FormInterface::class);
        $currentRequest = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn(FormSetOwnerEventListener::ACTION_ASSIGN);

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
        $event = new FormProcessEvent($form, $task);

        $this->listener->setOwnerAndLockForm($event);
    }

    public function testSetOwnerAndLockForm()
    {
        $task = new Task();
        $currentRequest = $this->createMock(Request::class);
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getAction')
            ->with($currentRequest)
            ->willReturn(FormSetOwnerEventListener::ACTION_ASSIGN);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityClassName')
            ->with($currentRequest)
            ->willReturn(User::class);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntityId')
            ->with($currentRequest)
            ->willReturn(1);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntity')
            ->with(User::class, 1)
            ->willReturn(new User());

        $form = $this->createMock(FormInterface::class);
        $ownerForm = $this->createMock(FormInterface::class);

        $ownerFormConfig = $this->createMock(FormConfigInterface::class);
        $ownerFormConfigType = $this->createMock(ResolvedFormTypeInterface::class);
        $formConfigInnerType = $this->createMock(UserAclSelectType::class);

        $ownerFormConfigType->expects($this->once())
            ->method('getInnerType')
            ->willReturn($formConfigInnerType);

        $ownerFormConfig->expects($this->once())
            ->method('getOptions')
            ->willReturn(
                [
                    'attr' => ['readonly' => false]
                ]
            );

        $ownerFormConfig->expects($this->once())
            ->method('getType')
            ->willReturn($ownerFormConfigType);

        $ownerForm->expects($this->once())
            ->method('getConfig')->willReturn($ownerFormConfig);

        $form->expects($this->once())
            ->method('get')
            ->with('owner')
            ->willReturn($ownerForm);

        $form->expects($this->once())
            ->method('add')
            ->with('owner', get_class($formConfigInnerType), ['attr' => ['readonly' => true]]);

        $event = $this->createMock(FormProcessEvent::class);
        $event->expects($this->once())
            ->method('getData')
            ->willReturn($task);

        $event->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $this->listener->setOwnerAndLockForm($event);
    }
}
