<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\EventListener;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\EventListener\SetCreatedByListener;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SetCreatedByListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var TokenStorageInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $tokenStorage;

    /** @var SetCreatedByListener */
    private $listener;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->listener = new SetCreatedByListener($this->tokenStorage);
    }

    public function testPrePersist()
    {
        $user = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage->expects($this->atLeastOnce())
            ->method('getToken')
            ->willReturn($token);

        $task = new Task();

        $this->listener->prePersist($task);

        $createdBy = $task->getCreatedBy();

        self::assertSame($this->tokenStorage->getToken()->getUser(), $createdBy);
    }

    public function testPrePersistOnExistingCreatedBy()
    {
        $user = new User();

        $this->tokenStorage->expects($this->never())
            ->method('getToken');

        $task = new Task();
        $task->setCreatedBy($user);

        $this->listener->prePersist($task);

        self::assertSame($user, $task->getCreatedBy());
    }

    public function testPrePersistWithNullToken()
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $task = new Task();

        $this->listener->prePersist($task);

        self::assertNull($task->getCreatedBy());
    }

    public function testPrePersistWithNotApplicableUser()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn(new \stdClass);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $task = new Task();

        $this->listener->prePersist($task);

        self::assertNull($task->getCreatedBy());
    }
}
