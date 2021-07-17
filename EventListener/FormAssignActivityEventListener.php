<?php

namespace Oro\Bundle\TaskBundle\EventListener;

use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\FormBundle\Event\FormHandler\AfterFormProcessEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listener adds activity to target entity
 */
class FormAssignActivityEventListener
{
    public const ACTION_ACTIVITY = 'activity';

    /**
     * @var EntityRoutingHelper
     */
    private $entityRoutingHelper;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ActivityManager
     */
    private $activityManager;

    public function __construct(
        ActivityManager $activityManager,
        EntityRoutingHelper $entityRoutingHelper,
        RequestStack $requestStack
    ) {
        $this->activityManager = $activityManager;
        $this->entityRoutingHelper = $entityRoutingHelper;
        $this->requestStack = $requestStack;
    }

    /**
     * Assign the Task activity to the target entity in case of adding task from the target entity page
     * only in case of 'contexts' field not added otherwise
     * Oro\Bundle\ActivityBundle\Form\Extension\ContextsExtension will handle it
     */
    public function assignActivityWithTask(AfterFormProcessEvent $event): void
    {
        // if we don't have "contexts" form field
        // we should save association between activity and target manually
        if ($event->getForm()->has('contexts')) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        $action = $this->entityRoutingHelper->getAction($request);

        if ($action !== self::ACTION_ACTIVITY) {
            return;
        }

        $targetEntityClass = $this->entityRoutingHelper->getEntityClassName($request);

        if (!$targetEntityClass) {
            return;
        }

        $targetEntityId = $this->entityRoutingHelper->getEntityId($request);

        if (!$targetEntityId) {
            return;
        }

        $entity = $this->entityRoutingHelper->getEntityReference($targetEntityClass, $targetEntityId);
        $data = $event->getData();

        $this->activityManager->addActivityTarget(
            $data,
            $entity
        );
    }
}
