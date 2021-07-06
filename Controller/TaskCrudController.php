<?php

namespace Oro\Bundle\TaskBundle\Controller;

use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TaskBundle\Form\Type\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This controller covers basic CRUD functionality for Task entity.
 */
class TaskCrudController extends AbstractController
{
    /**
     * @Route("/", name="oro_task_index")
     * @AclAncestor("oro_task_view")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render(
            '@OroTask/TaskCrud/index.html.twig',
            [
                'entity_class' => Task::class,
            ]
        );
    }

    /**
     * @Route("/view/{id}", name="oro_task_view", requirements={"id"="\d+"})
     * @Acl(
     *      id="oro_task_view",
     *      type="entity",
     *      class="OroTaskBundle:Task",
     *      permission="VIEW"
     * )
     *
     * @param Task $task
     *
     * @return Response
     */
    public function viewAction(Task $task)
    {
        return $this->render('@OroTask/TaskCrud/view.html.twig', ['entity' => $task]);
    }

    /**
     * @Route("/create", name="oro_task_create")
     * @Template("@OroTask/TaskCrud/update.html.twig")
     * @Acl(
     *      id="oro_task_create",
     *      type="entity",
     *      class="OroTaskBundle:Task",
     *      permission="CREATE"
     * )
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $defaultPriority = $this->getDoctrine()->getRepository(TaskPriority::class)->find('normal');
        if ($defaultPriority) {
            $task->setTaskPriority($defaultPriority);
        }

        return $this->update($request, $task);
    }

    /**
     * @Route("/update/{id}", name="oro_task_update", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="oro_task_update",
     *      type="entity",
     *      class="OroTaskBundle:Task",
     *      permission="EDIT"
     * )
     * @param Request $request
     * @param Task $task
     *
     * @return Response
     */
    public function updateAction(Request $request, Task $task)
    {
        return $this->update($request, $task);
    }

    /**
     * @param Request $request
     * @param Task $task
     *
     * @return Response|array
     */
    protected function update(Request $request, Task $task)
    {
        $updateResult = $this->get(UpdateHandlerFacade::class)->update(
            $task,
            $this->createForm(TaskType::class, $task),
            $this->get(TranslatorInterface::class)->trans('oro.task.saved_message'),
            $request,
            null,
            'oro_task_update'
        );

        return $updateResult;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            UpdateHandlerFacade::class,
            TranslatorInterface::class,
        ]);
    }
}
