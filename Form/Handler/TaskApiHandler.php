<?php

namespace Oro\Bundle\TaskBundle\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Oro\Bundle\SoapBundle\Controller\Api\FormAwareInterface;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Form\Type\TaskApiType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This API handler is obsolete implementation of REST API.
 * Only for backward compatibility. New JSON REST API should be use instead.
 * See ApiBundle
 */
class TaskApiHandler implements FormAwareInterface
{
    use RequestHandlerTrait;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var ObjectManager
     */
    protected $manager;

    public function __construct(FormFactory $formFactory, RequestStack $requestStack, ObjectManager $manager)
    {
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->manager = $manager;
    }

    /**
     * Process form
     *
     * @param  Task $entity
     * @return bool True on successful processing, false otherwise
     */
    public function process(Task $entity)
    {
        $form = $this->getForm();
        $form->setData($entity);

        $request = $this->requestStack->getCurrentRequest();

        if (\in_array($request->getMethod(), ['POST', 'PUT'], true)) {
            $this->submitPostPutRequest($form, $request);
            if ($form->isValid()) {
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->formFactory->createNamed('', TaskApiType::class);
    }

    /**
     * "Success" form handler
     */
    protected function onSuccess(Task $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
