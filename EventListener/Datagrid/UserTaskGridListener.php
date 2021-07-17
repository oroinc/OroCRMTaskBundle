<?php

namespace Oro\Bundle\TaskBundle\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

/**
 * This listener:
 *    - removes "ownerName" column
 *    - adds additional condition by the owner
 *
 * It is used for the user's tasks grid and tasks from My Tasks menu
 */
class UserTaskGridListener
{
    /** @var TokenAccessorInterface */
    protected $tokenAccessor;

    public function __construct(TokenAccessorInterface $tokenAccessor)
    {
        $this->tokenAccessor = $tokenAccessor;
    }

    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();
        $this->removeColumn($config, 'ownerName');
    }

    public function onBuildAfter(BuildAfter $event)
    {
        $datagrid = $event->getDatagrid();
        $datasource = $datagrid->getDatasource();
        if (!$datasource instanceof OrmDatasource) {
            return;
        }

        $parameters = $datagrid->getParameters();
        $userId = $parameters->get('userId');
        if (!$userId) {
            $userId = $this->tokenAccessor->getUserId();
        }
        $datasource->getQueryBuilder()
            ->andWhere(sprintf('task.owner = %d', (int)$userId));
    }

    /**
     * @param DatagridConfiguration $config
     * @param string $fieldName
     */
    protected function removeColumn(DatagridConfiguration $config, $fieldName)
    {
        $config->offsetUnsetByPath(sprintf('[columns][%s]', $fieldName));
        $config->offsetUnsetByPath(sprintf('[filters][columns][%s]', $fieldName));
        $config->offsetUnsetByPath(sprintf('[sorters][columns][%s]', $fieldName));
    }
}
