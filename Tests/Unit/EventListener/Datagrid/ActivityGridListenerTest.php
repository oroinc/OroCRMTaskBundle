<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\EventListener\Datagrid;

use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Datagrid\ParameterBag;
use Oro\Bundle\DataGridBundle\Datasource\ArrayDatasource\ArrayDatasource;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\TaskBundle\EventListener\Datagrid\ActivityGridListener;

class ActivityGridListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var EntityRoutingHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $entityRoutingHelper;

    /** @var ActivityManager|\PHPUnit\Framework\MockObject\MockObject */
    private $activityManager;

    /** @var ActivityGridListener */
    private $listener;

    protected function setUp(): void
    {
        $this->activityManager = $this->createMock(ActivityManager::class);
        $this->entityRoutingHelper = $this->createMock(EntityRoutingHelper::class);

        $this->listener = new ActivityGridListener(
            $this->activityManager,
            $this->entityRoutingHelper
        );
    }

    public function testOnBuildAfter()
    {
        $encodedEntityClass = 'Test_Entity';
        $entityClass = 'Test\Entity';
        $entityId = 123;

        $parameters = new ParameterBag(['entityClass' => $encodedEntityClass, 'entityId' => $entityId]);

        $queryBuilder = $this->createMock(QueryBuilder::class);

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

        $this->entityRoutingHelper->expects($this->once())
            ->method('resolveEntityClass')
            ->with($encodedEntityClass)
            ->willReturn($entityClass);

        $this->activityManager->expects($this->once())
            ->method('addFilterByTargetEntity')
            ->with($queryBuilder, $entityClass, $entityId);

        $event = new BuildAfter($datagrid);
        $this->listener->onBuildAfter($event);
    }

    public function testOnBuildAfterNonORM()
    {
        $datasource = $this->createMock(ArrayDatasource::class);

        $datagrid = $this->createMock(DatagridInterface::class);
        $datagrid->expects($this->once())
            ->method('getDatasource')
            ->willReturn($datasource);

        $this->activityManager->expects($this->never())
            ->method('addFilterByTargetEntity');

        $event = new BuildAfter($datagrid);
        $this->listener->onBuildAfter($event);
    }
}
