<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\EventListener\Datagrid;

use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Datagrid\ParameterBag;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessor;
use Oro\Bundle\TaskBundle\EventListener\Datagrid\UserTaskGridListener;

class UserTaskGridListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var UserTaskGridListener */
    private $listener;

    protected function setUp(): void
    {
        $this->listener = new UserTaskGridListener(
            $this->createMock(TokenAccessor::class)
        );
    }

    public function testOnBuildBefore()
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $config = $this->createMock(DatagridConfiguration::class);

        $config->expects($this->exactly(3))
            ->method('offsetUnsetByPath')
            ->withConsecutive(
                ['[columns][ownerName]'],
                ['[filters][columns][ownerName]'],
                ['[sorters][columns][ownerName]']
            );

        $event = new BuildBefore($datagrid, $config);
        $this->listener->onBuildBefore($event);
    }

    public function testOnBuildAfter()
    {
        $userId = 123;
        $parameters = new ParameterBag(['userId' => $userId]);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with(sprintf('task.owner = %d', (int)$userId));

        $datasource = $this->createMock(OrmDatasource::class);
        $datasource->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilder);

        $datagrid = $this->createMock(DatagridInterface::class);
        $datagrid->expects($this->once())
            ->method('getDatasource')
            ->willReturn($datasource);
        $datagrid->expects($this->once())
            ->method('getParameters')
            ->willReturn($parameters);

        $event = new BuildAfter($datagrid);
        $this->listener->onBuildAfter($event);
    }
}
