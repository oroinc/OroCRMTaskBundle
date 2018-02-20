<?php

namespace Oro\Bundle\TaskBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\TaskBundle\Entity\Repository\TaskRepository;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Form\Type\TaskType;
use Oro\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route(
     *      ".{_format}",
     *      name="oro_task_index",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     * @Acl(
     *      id="oro_task_view",
     *      type="entity",
     *      class="OroTaskBundle:Task",
     *      permission="VIEW"
     * )
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('oro_task.entity.class')
        ];
    }

    /**
     * @Route("/widget/sidebar-tasks/{perPage}", name="oro_task_widget_sidebar_tasks", defaults={"perPage" = 10})
     * @AclAncestor("oro_task_view")
     * @Template("OroTaskBundle:Task/widget:tasksWidget.html.twig")
     */
    public function tasksWidgetAction($perPage)
    {
        /** @var TaskRepository $repository */
        $repository = $this->getRepository('Oro\Bundle\TaskBundle\Entity\Task');
        $id = $this->getUser()->getId();
        $perPage = (int)$perPage;
        $tasks = $repository->getTasksAssignedTo($id, $perPage);

        return array('tasks' => $tasks);
    }

    /**
     * @Route("/create", name="oro_task_create")
     * @Acl(
     *      id="oro_task_create",
     *      type="entity",
     *      class="OroTaskBundle:Task",
     *      permission="CREATE"
     * )
     * @Template("OroTaskBundle:Task:update.html.twig")
     * @param Request $request
     * @return array
     */
    public function createAction(Request $request)
    {
        $task = new Task();

        $defaultPriority = $this->getRepository('OroTaskBundle:TaskPriority')->find('normal');
        if ($defaultPriority) {
            $task->setTaskPriority($defaultPriority);
        }

        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest('oro_task_create', $request);

        return $this->update($request, $task, $formAction);
    }

    /**
     * @return User
     */
    protected function getCurrentUser()
    {
        $token = $this->container->get('security.token_storage')->getToken();

        return $token ? $token->getUser() : null;
    }

    /**
     * @Route("/view/{id}", name="oro_task_view", requirements={"id"="\d+"})
     * @AclAncestor("oro_task_view")
     * @Template
     */
    public function viewAction(Task $task)
    {
        return array('entity' => $task);
    }

    /**
     * This action is used to render the list of tasks associated with the given entity
     * on the view page of this entity
     *
     * @Route(
     *      "/activity/view/{entityClass}/{entityId}",
     *      name="oro_task_activity_view"
     * )
     *
     * @AclAncestor("oro_task_view")
     * @Template
     */
    public function activityAction($entityClass, $entityId)
    {
        return array(
            'entity' => $this->get('oro_entity.routing_helper')->getEntity($entityClass, $entityId)
        );
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
     * @return array
     */
    public function updateAction(Request $request, Task $task)
    {
        $formAction = $this->get('router')->generate('oro_task_update', ['id' => $task->getId()]);

        return $this->update($request, $task, $formAction);
    }

    /**
     * @Route("/widget/info/{id}", name="oro_task_widget_info", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("oro_task_view")
     * @param Request $request
     * @param Task $entity
     * @return array
     */
    public function infoAction(Request $request, Task $entity)
    {
        return [
            'entity'         => $entity,
            'target'         => $this->getTargetEntity($request),
            'renderContexts' => true
        ];
    }

    /**
     * @Route("/user/{userId}", name="oro_task_user_tasks", requirements={"userId"="\d+"})
     * @AclAncestor("oro_task_view")
     * @Template
     */
    public function userTasksAction($userId)
    {
        return ['userId' => $userId];
    }

    /**
     * @Route("/my", name="oro_task_my_tasks")
     * @AclAncestor("oro_task_view")
     * @Template
     */
    public function myTasksAction()
    {
        return [];
    }

    /**
     * @param Request $request
     * @param Task $task
     * @param string $formAction
     * @return array
     */
    protected function update(Request $request, Task $task, $formAction)
    {
        $saved = false;
        if ($this->get('oro_task.form.handler.task')->process($task)) {
            if (!$request->get('_widgetContainer')) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('oro.task.saved_message')
                );

                return $this->get('oro_ui.router')->redirect($task);
            }
            $saved = true;
        }

        return array(
            'entity'     => $task,
            'saved'      => $saved,
            'form'       => $this->get('oro_task.form.handler.task')->getForm()->createView(),
            'formAction' => $formAction,
        );
    }

    /**
     * @return TaskType
     */
    protected function getFormType()
    {
        return $this->get('oro_task.form.handler.task')->getForm();
    }

    /**
     * @param string $entityName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($entityName)
    {
        return $this->getDoctrine()->getRepository($entityName);
    }

    /**
     * Get target entity
     *
     * @param Request $request
     * @return object|null
     */
    protected function getTargetEntity(Request $request)
    {
        $entityRoutingHelper = $this->get('oro_entity.routing_helper');
        $targetEntityClass   = $entityRoutingHelper->getEntityClassName($request, 'targetActivityClass');
        $targetEntityId      = $entityRoutingHelper->getEntityId($request, 'targetActivityId');
        if (!$targetEntityClass || !$targetEntityId) {
            return null;
        }

        return $entityRoutingHelper->getEntity($targetEntityClass, $targetEntityId);
    }
}
