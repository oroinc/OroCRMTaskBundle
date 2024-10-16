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

    #[\Override]
    public function isApplicableTarget($entityClass, $accessible = true)
    {
        return $this->activityAssociationHelper->isActivityAssociationEnabled(
            $entityClass,
            Task::class,
            $accessible
        );
    }

    /**
     * @param Task $entity
     */
    #[\Override]
    public function getSubject($entity)
    {
        return $entity->getSubject();
    }

    /**
     * @param Task $entity
     */
    #[\Override]
    public function getDescription($entity)
    {
        return trim(strip_tags($entity->getDescription()));
    }

    /**
     * @param Task $entity
     */
    #[\Override]
    public function getOwner($entity)
    {
        return $entity->getOwner();
    }

    /**
     * @param Task $entity
     */
    #[\Override]
    public function getCreatedAt($entity)
    {
        return $entity->getCreatedAt();
    }

    /**
     * @param Task $entity
     */
    #[\Override]
    public function getUpdatedAt($entity)
    {
        return $entity->getUpdatedAt();
    }

    #[\Override]
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
            'statusName' => $task->getStatus()->getInternalId(),
        ];
    }

    /**
     * @param Task $entity
     */
    #[\Override]
    public function getOrganization($entity)
    {
        return $entity->getOrganization();
    }

    #[\Override]
    public function getTemplate()
    {
        return '@OroTask/Task/js/activityItemTemplate.html.twig';
    }

    #[\Override]
    public function getRoutes($entity)
    {
        return [
            'itemView'   => 'oro_task_widget_info',
            'itemEdit'   => 'oro_task_update',
            'itemDelete' => 'oro_api_delete_task'
        ];
    }

    #[\Override]
    public function getActivityId($entity)
    {
        return $this->doctrineHelper->getSingleEntityIdentifier($entity);
    }

    #[\Override]
    public function isApplicable($entity)
    {
        if (\is_object($entity)) {
            return $entity instanceof Task;
        }

        return $entity === Task::class;
    }

    /**
     * @param Task $entity
     */
    #[\Override]
    public function getTargetEntities($entity)
    {
        return $entity->getActivityTargets();
    }

    #[\Override]
    public function isCommentsEnabled($entityClass)
    {
        return $this->commentAssociationHelper->isCommentAssociationEnabled($entityClass);
    }

    /**
     * @param Task $entity
     */
    #[\Override]
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

    #[\Override]
    public function isActivityListApplicable(ActivityList $activityList): bool
    {
        return true;
    }
}
