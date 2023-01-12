- [TaskBundle](#taskbundle)

TaskBundle
----------
* The following methods in class `TaskActivityListProvider`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Provider/TaskActivityListProvider.php#L42 "Oro\Bundle\TaskBundle\Provider\TaskActivityListProvider")</sup> were changed:
  > - `__construct(DoctrineHelper $doctrineHelper, ServiceLink $entityOwnerAccessorLink, ActivityAssociationHelper $activityAssociationHelper, CommentAssociationHelper $commentAssociationHelper)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Provider/TaskActivityListProvider.php#L42 "Oro\Bundle\TaskBundle\Provider\TaskActivityListProvider")</sup>
  > - `__construct(DoctrineHelper $doctrineHelper, ServiceLink $entityOwnerAccessorLink, ActivityAssociationHelper $activityAssociationHelper, CommentAssociationHelper $commentAssociationHelper)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Provider/TaskActivityListProvider.php#L42 "Oro\Bundle\TaskBundle\Provider\TaskActivityListProvider")</sup>

  > - `getRoutes()`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Provider/TaskActivityListProvider.php#L155 "Oro\Bundle\TaskBundle\Provider\TaskActivityListProvider")</sup>
  > - `getRoutes($activityEntity)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Provider/TaskActivityListProvider.php#L155 "Oro\Bundle\TaskBundle\Provider\TaskActivityListProvider")</sup>

* The `TaskApiHandler::__construct(FormInterface $form, Request $request, ObjectManager $manager)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Form/Handler/TaskApiHandler.php#L35 "Oro\Bundle\TaskBundle\Form\Handler\TaskApiHandler")</sup> method was changed to `TaskApiHandler::__construct(FormInterface $form, RequestStack $requestStack, ObjectManager $manager)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Form/Handler/TaskApiHandler.php#L36 "Oro\Bundle\TaskBundle\Form\Handler\TaskApiHandler")</sup>
* The `TaskHandler::__construct(FormInterface $form, Request $request, ObjectManager $manager, ActivityManager $activityManager, EntityRoutingHelper $entityRoutingHelper)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Form/Handler/TaskHandler.php#L39 "Oro\Bundle\TaskBundle\Form\Handler\TaskHandler")</sup> method was changed to `TaskHandler::__construct(FormInterface $form, RequestStack $requestStack, ObjectManager $manager, ActivityManager $activityManager, EntityRoutingHelper $entityRoutingHelper)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Form/Handler/TaskHandler.php#L40 "Oro\Bundle\TaskBundle\Form\Handler\TaskHandler")</sup>
* The following methods in class `TaskController`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L71 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup> were changed:
  > - `createAction()`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Controller/TaskController.php#L70 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup>
  > - `createAction(Request $request)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L71 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup>

  > - `update(Task $task, $formAction)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Controller/TaskController.php#L180 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup>
  > - `update(Request $request, Task $task, $formAction)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/TaskController.php#L188 "Oro\Bundle\TaskBundle\Controller\TaskController")</sup>

  > - `cgetAction()`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Controller/Api/Rest/TaskController.php#L79 "Oro\Bundle\TaskBundle\Controller\Api\Rest\TaskController")</sup>
  > - `cgetAction(Request $request)`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/3.0.0/Controller/Api/Rest/TaskController.php#L78 "Oro\Bundle\TaskBundle\Controller\Api\Rest\TaskController")</sup>

* The `TaskApiType::setDefaultOptions`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Form/Type/TaskApiType.php#L34 "Oro\Bundle\TaskBundle\Form\Type\TaskApiType::setDefaultOptions")</sup> method was removed.
* The `TaskType::setDefaultOptions`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Form/Type/TaskType.php#L89 "Oro\Bundle\TaskBundle\Form\Type\TaskType::setDefaultOptions")</sup> method was removed.
* The `TaskApiHandler::$request`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Form/Handler/TaskApiHandler.php#L22 "Oro\Bundle\TaskBundle\Form\Handler\TaskApiHandler::$request")</sup> property was removed.
* The `TaskHandler::$request`<sup>[[?]](https://github.com/oroinc/OroCRMTaskBundle/tree/2.6.0/Form/Handler/TaskHandler.php#L21 "Oro\Bundle\TaskBundle\Form\Handler\TaskHandler::$request")</sup> property was removed.

