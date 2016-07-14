<?php

namespace OroCRM\Bundle\TaskBundle\Controller\Api\Soap;

use Symfony\Component\Form\FormInterface;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Controller\Api\Soap\SoapController;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;

use OroCRM\Bundle\TaskBundle\Entity\TaskSoap;

class TaskController extends SoapController
{
    /**
     * @Soap\Method("getTasks")
     * @Soap\Param("page", phpType="int")
     * @Soap\Param("limit", phpType="int")
     * @Soap\Result(phpType = "OroCRM\Bundle\TaskBundle\Entity\TaskSoap[]")
     * @AclAncestor("orocrm_task_view")
     */
    public function cgetAction($page = 1, $limit = 10)
    {
        return $this->handleGetListRequest($page, $limit);
    }

    /**
     * @Soap\Method("getTask")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Result(phpType = "OroCRM\Bundle\TaskBundle\Entity\TaskSoap")
     * @AclAncestor("orocrm_task_view")
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * @Soap\Method("createTask")
     * @Soap\Param("task", phpType = "OroCRM\Bundle\TaskBundle\Entity\TaskSoap")
     * @Soap\Result(phpType = "int")
     * @AclAncestor("orocrm_task_create")
     */
    public function createAction($task)
    {
        return $this->handleCreateRequest();
    }

    /**
     * @Soap\Method("updateTask")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Param("task", phpType = "OroCRM\Bundle\TaskBundle\Entity\TaskSoap")
     * @Soap\Result(phpType = "boolean")
     * @AclAncestor("orocrm_task_update")
     */
    public function updateAction($id, $task)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * @Soap\Method("deleteTask")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Result(phpType = "boolean")
     * @AclAncestor("orocrm_task_delete")
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->container->get('orocrm_task.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->container->get('orocrm_task.form.api.soap');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->container->get('orocrm_task.form.handler.task_api.soap');
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
    protected function transformToSoapEntity($entity)
    {
        /** @var TaskSoap $entitySoap */
        $entitySoap = parent::transformToSoapEntity($entity);
        $workflowItems = $this->container->get('oro_workflow.manager')->getWorkflowItemsByEntity($entity);
        if (0 !== count($workflowItems)) {
            /** @var WorkflowItem $workflowItem */
            $workflowItem = array_shift($workflowItems);
            $entitySoap->setWorkflowItemId($workflowItem->getId());
            if ($workflowStep = $workflowItem->getCurrentStep()) {
                $entitySoap->setWorkflowStepId($workflowStep->getId());
            }
        }

        return $entitySoap;
    }
}
