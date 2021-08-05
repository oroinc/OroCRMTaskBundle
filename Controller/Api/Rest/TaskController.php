<?php

namespace Oro\Bundle\TaskBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\FormAwareInterface;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Request\Parameters\Filter\HttpDateTimeParameterFilter;
use Oro\Bundle\SoapBundle\Request\Parameters\Filter\IdentifierToReferenceFilter;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * REST API CRUD controller for Task entity.
 */
class TaskController extends RestController
{
    const FIELD_WORKFLOW_ITEM = 'workflowItem';
    const FIELD_WORKFLOW_STEP = 'workflowStep';

    /**
     * REST GET list
     *
     * @QueryParam(
     *      name="page",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Page number, starting from 1. Defaults to 1."
     * )
     * @QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Number of items per page. defaults to 10."
     * )
     * @QueryParam(
     *     name="createdAt",
     *     requirements="\d{4}(-\d{2}(-\d{2}([T ]\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|([-+]\d{2}(:?\d{2})?))?)?)?)?",
     *     nullable=true,
     *     description="Date in RFC 3339 format. For example: 2009-11-05T13:15:30Z, 2008-07-01T22:35:17+08:00"
     * )
     * @QueryParam(
     *     name="updatedAt",
     *     requirements="\d{4}(-\d{2}(-\d{2}([T ]\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|([-+]\d{2}(:?\d{2})?))?)?)?)?",
     *     nullable=true,
     *     description="Date in RFC 3339 format. For example: 2009-11-05T13:15:30Z, 2008-07-01T22:35:17+08:00"
     * )
     * @QueryParam(
     *     name="ownerId",
     *     requirements="\d+",
     *     nullable=true,
     *     description="Id of owner assignee"
     * )
     * @QueryParam(
     *     name="ownerUsername",
     *     requirements=".+",
     *     nullable=true,
     *     description="Username of owner assignee"
     * )
     * @ApiDoc(
     *      description="Get all task items",
     *      resource=true
     * )
     * @AclAncestor("oro_task_view")
     * @param Request $request
     * @return Response
     */
    public function cgetAction(Request $request)
    {
        $page  = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit', self::ITEMS_PER_PAGE);

        $dateParamFilter  = new HttpDateTimeParameterFilter();
        $filterParameters = [
            'createdAt'     => $dateParamFilter,
            'updatedAt'     => $dateParamFilter,
            'ownerId'       => new IdentifierToReferenceFilter($this->getDoctrine(), User::class),
            'ownerUsername' => new IdentifierToReferenceFilter($this->getDoctrine(), User::class, 'username'),
        ];
        $map              = array_fill_keys(['ownerId', 'ownerUsername'], 'owner');

        $criteria = $this->getFilterCriteria($this->getSupportedQueryParameters('cgetAction'), $filterParameters, $map);

        return $this->handleGetListRequest($page, $limit, $criteria);
    }

    /**
     * REST GET item
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Get task item",
     *      resource=true
     * )
     * @AclAncestor("oro_task_view")
     * @return Response
     */
    public function getAction(int $id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT
     *
     * @param int $id Task item id
     *
     * @ApiDoc(
     *      description="Update task",
     *      resource=true
     * )
     * @AclAncestor("oro_task_update")
     * @return Response
     */
    public function putAction(int $id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new task
     *
     * @ApiDoc(
     *      description="Create new task",
     *      resource=true
     * )
     * @AclAncestor("oro_task_create")
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete Task",
     *      resource=true
     * )
     * @Acl(
     *      id="oro_task_delete",
     *      type="entity",
     *      permission="DELETE",
     *      class="OroTaskBundle:Task"
     * )
     * @return Response
     */
    public function deleteAction(int $id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->get('oro_task.manager.api');
    }

    /**
     * @return FormAwareInterface
     */
    public function getFormHandler()
    {
        return $this->get('oro_task.form.handler.task_api');
    }

    /**
     * {@inheritdoc}
     */
    protected function transformEntityField($field, &$value)
    {
        switch ($field) {
            case 'taskPriority':
                if ($value) {
                    $value = $value->getName();
                }
                break;
            case 'owner':
            default:
                parent::transformEntityField($field, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function fixFormData(array &$data, $entity)
    {
        parent::fixFormData($data, $entity);

        unset($data['id']);
        unset($data['updatedAt']);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function getPreparedItem($entity, $resultFields = [])
    {
        $entityData = parent::getPreparedItem($entity, $resultFields);
        $workflowItems = $this->container->get('oro_workflow.manager')->getWorkflowItemsByEntity($entity);
        if (0 !== count($workflowItems)) {
            /** @var WorkflowItem $workflowItem */
            $workflowItem = array_shift($workflowItems);
            if (!$resultFields || in_array(self::FIELD_WORKFLOW_ITEM, $resultFields, true)) {
                $entityData[self::FIELD_WORKFLOW_ITEM] = $workflowItem->getId();
            }
            $workflowStep = $workflowItem->getCurrentStep();
            if ($workflowStep && (!$resultFields || in_array(self::FIELD_WORKFLOW_STEP, $resultFields, true))) {
                $entityData[self::FIELD_WORKFLOW_STEP] = $workflowStep->getId();
            }
        }

        return $entityData;
    }
}
