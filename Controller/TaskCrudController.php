<?php

namespace Oro\Bundle\TaskBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TaskBundle\Form\Type\TaskType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This controller covers basic CRUD functionality for Task entity.
 */
class TaskCrudController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: '/', name: 'oro_task_index')]
    #[AclAncestor('oro_task_view')]
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
     *
     * @param Task $task
     * @return Response
     */
    #[Route(path: '/view/{id}', name: 'oro_task_view', requirements: ['id' => '\d+'])]
    #[Acl(id: 'oro_task_view', type: 'entity', class: Task::class, permission: 'VIEW')]
    public function viewAction(Task $task)
    {
        return $this->render('@OroTask/TaskCrud/view.html.twig', ['entity' => $task]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/create', name: 'oro_task_create')]
    #[Template('@OroTask/TaskCrud/update.html.twig')]
    #[Acl(id: 'oro_task_create', type: 'entity', class: Task::class, permission: 'CREATE')]
    public function createAction(Request $request)
    {
        $task = new Task();
        $defaultPriority = $this->container->get('doctrine')->getRepository(TaskPriority::class)->find('normal');
        if ($defaultPriority) {
            $task->setTaskPriority($defaultPriority);
        }

        return $this->update($request, $task);
    }

    /**
     * @param Request $request
     * @param Task $task
     * @return Response
     */
    #[Route(path: '/update/{id}', name: 'oro_task_update', requirements: ['id' => '\d+'])]
    #[Template('@OroTask/TaskCrud/update.html.twig')]
    #[Acl(id: 'oro_task_update', type: 'entity', class: Task::class, permission: 'EDIT')]
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
        $updateResult = $this->container->get(UpdateHandlerFacade::class)->update(
            $task,
            $this->createForm(TaskType::class, $task),
            $this->container->get(TranslatorInterface::class)->trans('oro.task.saved_message'),
            $request,
            null,
            'oro_task_update'
        );

        return $updateResult;
    }

    #[\Override]
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            UpdateHandlerFacade::class,
            TranslatorInterface::class,
            'doctrine' => ManagerRegistry::class,
        ]);
    }
}
