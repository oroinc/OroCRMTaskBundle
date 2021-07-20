<?php

namespace Oro\Bundle\TaskBundle\EventListener;

use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\FormBundle\Event\FormHandler\FormProcessEvent;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listener set owner value and lock "owner" form field
 */
class FormSetOwnerEventListener
{
    public const ACTION_ASSIGN = 'assign';

    /**
     * @var EntityRoutingHelper
     */
    private $entityRoutingHelper;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityRoutingHelper $entityRoutingHelper, RequestStack $requestStack)
    {
        $this->entityRoutingHelper = $entityRoutingHelper;
        $this->requestStack = $requestStack;
    }

    /**
     * Lock "owner" field of the form and preset it when assigning task from the user view page.
     */
    public function setOwnerAndLockForm(FormProcessEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        $action = $this->entityRoutingHelper->getAction($request);

        if ($action !== self::ACTION_ASSIGN) {
            return;
        }

        $targetEntityClass = $this->entityRoutingHelper->getEntityClassName($request);

        if (!$targetEntityClass || !is_a($targetEntityClass, User::class, true)) {
            return;
        }

        $targetEntityId = $this->entityRoutingHelper->getEntityId($request);

        if (!$targetEntityId) {
            return;
        }

        $data = $event->getData();
        $form = $event->getForm();

        $data->setOwner(
            $this->entityRoutingHelper->getEntity($targetEntityClass, $targetEntityId)
        );

        FormUtils::replaceFieldOptionsRecursive($form, 'owner', [
            'attr' => ['readonly' => true],
        ]);
    }
}
