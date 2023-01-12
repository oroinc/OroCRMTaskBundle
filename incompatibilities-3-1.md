- [TaskBundle](#taskbundle)

TaskBundle
----------
* The `TaskHandler`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Form/Handler/TaskHandler.php#L14 "Oro\Bundle\TaskBundle\Form\Handler\TaskHandler")</sup> class was removed.
* The `TaskType::getName`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Form/Type/TaskType.php#L107 "Oro\Bundle\TaskBundle\Form\Type\TaskType::getName")</sup> method was removed.
* The following methods in class `Task`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L223 "Oro\Bundle\TaskBundle\Entity\Task")</sup> were removed:
   - `setId`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L223 "Oro\Bundle\TaskBundle\Entity\Task::setId")</sup>
   - `getCreatedAt`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L407 "Oro\Bundle\TaskBundle\Entity\Task::getCreatedAt")</sup>
   - `setCreatedAt`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L416 "Oro\Bundle\TaskBundle\Entity\Task::setCreatedAt")</sup>
   - `getUpdatedAt`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L426 "Oro\Bundle\TaskBundle\Entity\Task::getUpdatedAt")</sup>
   - `setUpdatedAt`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L436 "Oro\Bundle\TaskBundle\Entity\Task::setUpdatedAt")</sup>
   - `isUpdatedAtSet`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L451 "Oro\Bundle\TaskBundle\Entity\Task::isUpdatedAtSet")</sup>
* The following methods in class `TaskController`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L36 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup> were removed:
   - `indexAction`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L36 "Oro\Bundle\TaskBundle\Controller\TaskController::indexAction")</sup>
   - `createAction`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L71 "Oro\Bundle\TaskBundle\Controller\TaskController::createAction")</sup>
   - `getCurrentUser`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L89 "Oro\Bundle\TaskBundle\Controller\TaskController::getCurrentUser")</sup>
   - `viewAction`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L101 "Oro\Bundle\TaskBundle\Controller\TaskController::viewAction")</sup>
   - `updateAction`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L138 "Oro\Bundle\TaskBundle\Controller\TaskController::updateAction")</sup>
   - `update`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L188 "Oro\Bundle\TaskBundle\Controller\TaskController::update")</sup>
   - `getFormType`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L214 "Oro\Bundle\TaskBundle\Controller\TaskController::getFormType")</sup>
   - `getRepository`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L223 "Oro\Bundle\TaskBundle\Controller\TaskController::getRepository")</sup>
   - `getForm`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/Api/Rest/TaskController.php#L180 "Oro\Bundle\TaskBundle\Controller\Api\Rest\TaskController::getForm")</sup>
* The `TaskApiHandler::__construct(FormInterface $form, RequestStack $requestStack, ObjectManager $manager)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Form/Handler/TaskApiHandler.php#L36 "Oro\Bundle\TaskBundle\Form\Handler\TaskApiHandler")</sup> method was changed to `TaskApiHandler::__construct(FormFactory $formFactory, RequestStack $requestStack, ObjectManager $manager)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.1.0/Form/Handler/TaskApiHandler.php#L43 "Oro\Bundle\TaskBundle\Form\Handler\TaskApiHandler")</sup>
* The `SetCreatedByListener::prePersist(Task $task, LifecycleEventArgs $args)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/EventListener/SetCreatedByListener.php#L29 "Oro\Bundle\TaskBundle\EventListener\SetCreatedByListener")</sup> method was changed to `SetCreatedByListener::prePersist(Task $task)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.1.0/EventListener/SetCreatedByListener.php#L30 "Oro\Bundle\TaskBundle\EventListener\SetCreatedByListener")</sup>
* The `Task::setTaskPriority($taskPriority)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L303 "Oro\Bundle\TaskBundle\Entity\Task")</sup> method was changed to `Task::setTaskPriority(TaskPriority $taskPriority)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.1.0/Entity/Task.php#L266 "Oro\Bundle\TaskBundle\Entity\Task")</sup>
* The `TaskController::tasksWidgetAction($perPage)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L48 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup> method was changed to `TaskController::tasksWidgetAction($perPage)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.1.0/Controller/TaskController.php#L33 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup>
* The `TaskApiHandler::$form`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Form/Handler/TaskApiHandler.php#L18 "Oro\Bundle\TaskBundle\Form\Handler\TaskApiHandler::$form")</sup> property was removed.
* The following properties in class `Task`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L192 "Oro\Bundle\TaskBundle\Entity\Task")</sup> were removed:
   - `$createdAt`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L192 "Oro\Bundle\TaskBundle\Entity\Task::$createdAt")</sup>
   - `$updatedAt`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L206 "Oro\Bundle\TaskBundle\Entity\Task::$updatedAt")</sup>
   - `$updatedAtSet`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Entity/Task.php#L211 "Oro\Bundle\TaskBundle\Entity\Task::$updatedAtSet")</sup>

