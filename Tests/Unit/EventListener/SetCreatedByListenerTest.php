<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\TaskBundle\EventListener\SetCreatedByListener;
use Oro\Component\Testing\Unit\EntityTrait;

class SetCreatedByListenerTest extends \PHPUnit_Framework_TestCase
{
    use EntityTrait;

    public function testPrePersistFillsCreatedByIfUserIsLoggedIn()
    {
        $user = $this->getEntity(User::class);

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->any())->method('getUser')->willReturn($user);

        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenStorage->expects($this->any())->method('getToken')->willReturn($token);

        $task = $this->getEntity(Task::class);
        $args = $this->createMock(LifecycleEventArgs::class);

        $listener = new SetCreatedByListener($this->tokenStorage);
        $listener->prePersist($task, $args);

        $this->assertSame($user, $task->getCreatedBy());
    }

    public function testPrePersistDoesNothingIfUserIsNotLoggedIn()
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);

        $task = $this->getEntity(Task::class);
        $args = $this->createMock(LifecycleEventArgs::class);

        $listener = new SetCreatedByListener($this->tokenStorage);
        $listener->prePersist($task, $args);

        $this->assertSame(null, $task->getCreatedBy());
    }

    public function testPrePersistDoesNothingIfCreatedByIsAlreadyFilled()
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenStorage->expects($this->never())->method('getToken');

        $task = $this->getEntity(Task::class, ['createdBy' => $this->getEntity(User::class)]);
        $args = $this->createMock(LifecycleEventArgs::class);

        $listener = new SetCreatedByListener($this->tokenStorage);
        $listener->prePersist($task, $args);
    }
}
