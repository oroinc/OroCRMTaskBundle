<?php

namespace Oro\Bundle\TaskBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Oro\Bundle\TaskBundle\Entity\Task;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TaskHandler
{
    use RequestHandlerTrait;

    /** @var FormInterface */
    protected $form;

    /** @var RequestStack */
    protected $requestStack;

    /** @var ObjectManager */
    protected $manager;

    /** @var  ActivityManager */
    protected $activityManager;

    /** @var EntityRoutingHelper */
    protected $entityRoutingHelper;

    /**
     * @param FormInterface       $form
     * @param RequestStack        $requestStack
     * @param ObjectManager       $manager
     * @param ActivityManager     $activityManager
     * @param EntityRoutingHelper $entityRoutingHelper
     */
    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        ObjectManager $manager,
        ActivityManager $activityManager,
        EntityRoutingHelper $entityRoutingHelper
    ) {
        $this->form                = $form;
        $this->requestStack        = $requestStack;
        $this->manager             = $manager;
        $this->activityManager     = $activityManager;
        $this->entityRoutingHelper = $entityRoutingHelper;
    }

    /**
     * Process form
     *
     * @param  Task $entity
     *
     * @return bool  True on successful processing, false otherwise
     */
    public function process(Task $entity)
    {
        $request = $this->requestStack->getCurrentRequest();
        $action            = $this->entityRoutingHelper->getAction($request);
        $targetEntityClass = $this->entityRoutingHelper->getEntityClassName($request);
        $targetEntityId    = $this->entityRoutingHelper->getEntityId($request);

        if ($targetEntityClass
            && !$entity->getId()
            && $request->getMethod() === 'GET'
            && $action === 'assign'
            && is_a($targetEntityClass, 'Oro\Bundle\UserBundle\Entity\User', true)
        ) {
            $entity->setOwner(
                $this->entityRoutingHelper->getEntity($targetEntityClass, $targetEntityId)
            );
            FormUtils::replaceFieldOptionsRecursive($this->form, 'owner', [
                'attr' => ['readonly' => true]
            ]);
        }

        $this->form->setData($entity);

        if (in_array($request->getMethod(), ['POST', 'PUT'], true)) {
            $this->submitPostPutRequest($this->form, $request);

            if ($this->form->isValid()) {
                // TODO: should be refactored after finishing BAP-8722
                // Contexts handling should be moved to common for activities form handler
                if ($this->form->has('contexts')) {
                    $contexts = $this->form->get('contexts')->getData();
                    $this->activityManager->setActivityTargets($entity, $contexts);
                } elseif ($targetEntityClass && $action === 'activity') {
                    // if we don't have "contexts" form field
                    // we should save association between activity and target manually
                    $this->activityManager->addActivityTarget(
                        $entity,
                        $this->entityRoutingHelper->getEntityReference($targetEntityClass, $targetEntityId)
                    );
                }
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     *
     * @param Task $entity
     */
    protected function onSuccess(Task $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }

    /**
     * Get form, that build into handler, via handler service
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
