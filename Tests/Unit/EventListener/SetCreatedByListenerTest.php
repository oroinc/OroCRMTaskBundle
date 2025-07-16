<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\EventListener;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\EventListener\SetCreatedByListener;
use Oro\Bundle\UserBundle\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;

class SetCreatedByListenerTest extends TestCase
{
    private TokenStorageInterface&MockObject $tokenStorage;
    private SetCreatedByListener $listener;

    #[\Override]
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->listener = new SetCreatedByListener($this->tokenStorage);
    }

    public function testPrePersist(): void
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

    public function testPrePersistOnExistingCreatedBy(): void
    {
        $user = new User();

        $this->tokenStorage->expects($this->never())
            ->method('getToken');

        $task = new Task();
        $task->setCreatedBy($user);

        $this->listener->prePersist($task);

        self::assertSame($user, $task->getCreatedBy());
    }

    public function testPrePersistWithNullToken(): void
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $task = new Task();

        $this->listener->prePersist($task);

        self::assertNull($task->getCreatedBy());
    }

    public function testPrePersistWithNotApplicableUser(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn(new InMemoryUser('test', null));

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $task = new Task();

        $this->listener->prePersist($task);

        self::assertNull($task->getCreatedBy());
    }
}
