<?php

namespace Oro\Bundle\TaskBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TaskBundle\Form\Type\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller covers basic CRUD functionality for Task entity.
 */
class TaskCRUDController extends Controller
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
            '@OroTask/TaskCRUD/index.html.twig',
            [
                'entity_class' => $this->container->getParameter('oro_task.entity.class'),
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
        return $this->render('@OroTask/TaskCRUD/view.html.twig', ['entity' => $task]);
    }

    /**
     * @Route("/create", name="oro_task_create")
     * @Template("OroTaskBundle:TaskCRUD:update.html.twig")
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
        $updateResult = $this->get('oro_form.update_handler')->update(
            $task,
            $this->createForm(TaskType::class, $task),
            $this->get('translator')->trans('oro.task.saved_message'),
            $request,
            null,
            'oro_task_update'
        );

        return $updateResult;
    }
}
