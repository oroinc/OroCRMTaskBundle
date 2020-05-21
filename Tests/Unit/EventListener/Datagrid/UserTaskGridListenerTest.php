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

    /** @var TokenAccessor|\PHPUnit\Framework\MockObject\MockObject */
    private $tokenAccessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->tokenAccessor = $this->getMockBuilder(TokenAccessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new UserTaskGridListener(
            $this->tokenAccessor
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testOnBuildBefore()
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $config = $this->createMock(DatagridConfiguration::class);

        $config->expects($this->at(0))
            ->method('offsetUnsetByPath')
            ->with('[columns][ownerName]');
        $config->expects($this->at(1))
            ->method('offsetUnsetByPath')
            ->with('[filters][columns][ownerName]');
        $config->expects($this->at(2))
            ->method('offsetUnsetByPath')
            ->with('[sorters][columns][ownerName]');

        $event = new BuildBefore($datagrid, $config);
        $this->listener->onBuildBefore($event);
    }

    /**
     * {@inheritdoc}
     */
    public function testOnBuildAfter()
    {
        $userId = 123;
        $parameters = new ParameterBag(['userId' => $userId]);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with(sprintf('task.owner = %d', (int)$userId));

        $datasource = $this->getMockBuilder(OrmDatasource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $datasource->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilder);

        $datagrid = $this->createMock(DatagridInterface::class);
        $datagrid->expects($this->once())
            ->method('getDatasource')
            ->will($this->returnValue($datasource));
        $datagrid->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($parameters));

        $event = new BuildAfter($datagrid);
        $this->listener->onBuildAfter($event);
    }
}
