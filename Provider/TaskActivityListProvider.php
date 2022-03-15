<?php

namespace Oro\Bundle\TaskBundle\Provider;

use Oro\Bundle\ActivityBundle\Tools\ActivityAssociationHelper;
use Oro\Bundle\ActivityListBundle\Entity\ActivityList;
use Oro\Bundle\ActivityListBundle\Entity\ActivityOwner;
use Oro\Bundle\ActivityListBundle\Model\ActivityListDateProviderInterface;
use Oro\Bundle\ActivityListBundle\Model\ActivityListProviderInterface;
use Oro\Bundle\CommentBundle\Model\CommentProviderInterface;
use Oro\Bundle\CommentBundle\Tools\CommentAssociationHelper;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Component\DependencyInjection\ServiceLink;

/**
 * Provides a way to use Task entity in an activity list.
 */
class TaskActivityListProvider implements
    ActivityListProviderInterface,
    CommentProviderInterface,
    ActivityListDateProviderInterface
{
    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /** @var ServiceLink */
    protected $entityOwnerAccessorLink;

    /** @var ActivityAssociationHelper */
    protected $activityAssociationHelper;

    /** @var CommentAssociationHelper */
    protected $commentAssociationHelper;

    public function __construct(
        DoctrineHelper $doctrineHelper,
        ServiceLink $entityOwnerAccessorLink,
        ActivityAssociationHelper $activityAssociationHelper,
        CommentAssociationHelper $commentAssociationHelper
    ) {
        $this->doctrineHelper            = $doctrineHelper;
        $this->entityOwnerAccessorLink   = $entityOwnerAccessorLink;
        $this->activityAssociationHelper = $activityAssociationHelper;
        $this->commentAssociationHelper  = $commentAssociationHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableTarget($entityClass, $accessible = true)
    {
        return $this->activityAssociationHelper->isActivityAssociationEnabled(
            $entityClass,
            Task::class,
            $accessible
        );
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getSubject($entity)
    {
        return $entity->getSubject();
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getDescription($entity)
    {
        return trim(strip_tags($entity->getDescription()));
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getOwner($entity)
    {
        return $entity->getOwner();
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getCreatedAt($entity)
    {
        return $entity->getCreatedAt();
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getUpdatedAt($entity)
    {
        return $entity->getUpdatedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ActivityList $activityList)
    {
        /** @var Task $task */
        $task = $this->doctrineHelper
            ->getEntityManager($activityList->getRelatedActivityClass())
            ->getRepository($activityList->getRelatedActivityClass())
            ->find($activityList->getRelatedActivityId());

        if (!$task->getStatus()) {
            return [
                'statusId' => null,
                'statusName' => null,
            ];
        }

        return [
            'statusId' => $task->getStatus()->getId(),
            'statusName' => $task->getStatus()->getName(),
        ];
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getOrganization($entity)
    {
        return $entity->getOrganization();
    }

    /**
     * {@inheritdoc
     */
    public function getTemplate()
    {
        return '@OroTask/Task/js/activityItemTemplate.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes($entity)
    {
        return [
            'itemView'   => 'oro_task_widget_info',
            'itemEdit'   => 'oro_task_update',
            'itemDelete' => 'oro_api_delete_task'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getActivityId($entity)
    {
        return $this->doctrineHelper->getSingleEntityIdentifier($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable($entity)
    {
        if (\is_object($entity)) {
            return $entity instanceof Task;
        }

        return $entity === Task::class;
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getTargetEntities($entity)
    {
        return $entity->getActivityTargets();
    }

    /**
     * {@inheritdoc}
     */
    public function isCommentsEnabled($entityClass)
    {
        return $this->commentAssociationHelper->isCommentAssociationEnabled($entityClass);
    }

    /**
     * {@inheritdoc}
     * @param Task $entity
     */
    public function getActivityOwners($entity, ActivityList $activityList)
    {
        $organization = $this->getOrganization($entity);
        $owner = $this->entityOwnerAccessorLink->getService()->getOwner($entity);

        if (!$organization || !$owner) {
            return [];
        }

        $activityOwner = new ActivityOwner();
        $activityOwner->setActivity($activityList);
        $activityOwner->setOrganization($organization);
        $activityOwner->setUser($owner);

        return [$activityOwner];
    }

    /**
     * {@inheritDoc}
     */
    public function isActivityListApplicable(ActivityList $activityList): bool
    {
        return true;
    }
}
