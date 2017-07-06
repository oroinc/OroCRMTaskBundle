<?php

namespace Oro\Bundle\TaskBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\UserBundle\Entity\User;

class SetCreatedByListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Task $task
     * @param LifecycleEventArgs $args
     */
    public function prePersist(Task $task, LifecycleEventArgs $args)
    {
        $token = $this->tokenStorage->getToken();
        if ($token !== null && $token->getUser() instanceof User) {
            $task->setCreatedBy($token->getUser());
        }
    }
}
