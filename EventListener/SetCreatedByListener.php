<?php

namespace Oro\Bundle\TaskBundle\EventListener;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Sets createdBy field if it's not set
 */
class SetCreatedByListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(Task $task)
    {
        if ($task->getCreatedBy() !== null) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token !== null && $token->getUser() instanceof User) {
            $task->setCreatedBy($token->getUser());
        }
    }
}
