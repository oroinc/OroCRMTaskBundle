<?php

namespace Oro\Bundle\TaskBundle\Entity\Repository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\WorkflowBundle\Helper\WorkflowQueryTrait;
use Oro\Component\DoctrineUtils\ORM\QueryBuilderUtil;

/**
 * Repository provides a way to get Task entities from the storage
 */
class TaskRepository extends EntityRepository
{
    use WorkflowQueryTrait;
    private const CLOSED_STATE = 'closed';

    /**
     * @param int $userId
     * @param int $limit
     *
     * @return Task[]
     */
    public function getTasksAssignedTo($userId, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('task');
        $this->joinWorkflowStep($queryBuilder, 'workflowStep');

        return $queryBuilder
            ->where('task.owner = :assignedTo AND workflowStep.name != :step')
            ->orderBy('task.dueDate', 'ASC')
            ->addOrderBy('workflowStep.id', 'ASC')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->setParameter('assignedTo', $userId)
            ->setParameter('step', self::CLOSED_STATE)
            ->getQuery()
            ->execute();
    }

    /**
     * Returns a query builder which can be used to get a list of tasks filtered by start and end dates
     *
     * @param int $userId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string[] $extraFields
     *
     * @return QueryBuilder
     */
    public function getTaskListByTimeIntervalQueryBuilder($userId, $startDate, $endDate, $extraFields = [])
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id, t.subject, t.description, t.dueDate, t.createdAt, t.updatedAt')
            ->where('t.owner = :assignedTo AND t.dueDate >= :start AND t.dueDate <= :end')
            ->setParameter('assignedTo', $userId)
            ->setParameter('start', $startDate, Types::DATETIME_MUTABLE)
            ->setParameter('end', $endDate, Types::DATETIME_MUTABLE);
        if ($extraFields) {
            foreach ($extraFields as $field) {
                $qb->addSelect(QueryBuilderUtil::getField('t', $field));
            }
        }

        return $qb;
    }
}
