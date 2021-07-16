<?php

namespace Oro\Bundle\TaskBundle\Controller;

use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\TaskBundle\Entity\Repository\TaskRepository;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This controller covers widget-related functionality for Task entity.
 */
class TaskController extends AbstractController
{
    /**
     * @Route(
     *     "/widget/sidebar-tasks/{perPage}",
     *     name="oro_task_widget_sidebar_tasks",
     *     defaults={"perPage" = 10},
     *     requirements={"perPage"="\d+"}
     * )
     * @AclAncestor("oro_task_view")
     */
    public function tasksWidgetAction(int $perPage): Response
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->getDoctrine()->getRepository(Task::class);
        $userId = $this->getUser()->getId();
        $tasks = $taskRepository->getTasksAssignedTo($userId, $perPage);

        return $this->render('@OroTask/Task/widget/tasksWidget.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/widget/info/{id}", name="oro_task_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("oro_task_view")
     * @Template("@OroTask/Task/widget/info.html.twig")
     */
    public function infoAction(Request $request, Task $entity): array
    {
        $targetEntity = $this->getTargetEntity($request);
        $renderContexts = null !== $targetEntity;

        return [
            'entity' => $entity,
            'target' => $targetEntity,
            'renderContexts' => $renderContexts,
        ];
    }

    /**
     * This action is used to render the list of tasks associated with the given entity
     * on the view page of this entity
     *
     * @Route(
     *      "/activity/view/{entityClass}/{entityId}",
     *      name="oro_task_activity_view",
     *      requirements={"entityClass"="\w+", "entityId"="\d+"}
     * )
     *
     * @AclAncestor("oro_task_view")
     */
    public function activityAction(string $entityClass, int $entityId): Response
    {
        return $this->render(
            '@OroTask/Task/activity.html.twig',
            [
                'entity' => $this->get(EntityRoutingHelper::class)->getEntity($entityClass, $entityId),
            ]
        );
    }

    /**
     * @Route("/user/{user}", name="oro_task_user_tasks", requirements={"user"="\d+"})
     * @AclAncestor("oro_task_view")
     */
    public function userTasksAction(User $user): Response
    {
        return $this->render('@OroTask/Task/widget/userTasks.html.twig', ['entity' => $user]);
    }

    /**
     * @Route("/my", name="oro_task_my_tasks")
     * @AclAncestor("oro_task_view")
     */
    public function myTasksAction(): Response
    {
        return $this->render('@OroTask/Task/myTasks.html.twig');
    }

    /**
     * Get target entity
     *
     * @param Request $request
     *
     * @return object|null
     */
    protected function getTargetEntity(Request $request)
    {
        $entityRoutingHelper = $this->get(EntityRoutingHelper::class);
        $targetEntityClass = $entityRoutingHelper->getEntityClassName($request, 'targetActivityClass');
        $targetEntityId = $entityRoutingHelper->getEntityId($request, 'targetActivityId');
        if (!$targetEntityClass || !$targetEntityId) {
            return null;
        }

        return $entityRoutingHelper->getEntity($targetEntityClass, $targetEntityId);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EntityRoutingHelper::class,
            ]
        );
    }
}
