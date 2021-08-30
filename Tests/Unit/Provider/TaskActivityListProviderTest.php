<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oro\Bundle\ActivityBundle\Tools\ActivityAssociationHelper;
use Oro\Bundle\ActivityListBundle\Entity\ActivityList;
use Oro\Bundle\CommentBundle\Tools\CommentAssociationHelper;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityExtendBundle\Tests\Unit\Fixtures\TestEnumValue;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Provider\TaskActivityListProvider;
use Oro\Bundle\TaskBundle\Tests\Unit\Stub\TaskStub;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Component\DependencyInjection\ServiceLink;

class TaskActivityListProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var DoctrineHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $doctrineHelper;

    /** @var ServiceLink|\PHPUnit\Framework\MockObject\MockObject */
    private $entityOwnerAccessorLink;

    /** @var ActivityAssociationHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $activityAssociationHelper;

    /** @var CommentAssociationHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $commentAssociationHelper;

    /** @var TaskActivityListProvider */
    private $provider;

    /** @var Task */
    private $task;

    /** @var ActivityList */
    private $activityList;

    protected function setUp(): void
    {
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);
        $this->entityOwnerAccessorLink = $this->createMock(ServiceLink::class);
        $this->activityAssociationHelper = $this->createMock(ActivityAssociationHelper::class);
        $this->commentAssociationHelper = $this->createMock(CommentAssociationHelper::class);
        $this->activityList = $this->createMock(ActivityList::class);

        $this->activityList->expects($this->any())
            ->method('getRelatedActivityClass')
            ->willReturn(Task::class);
        $this->activityList->expects($this->any())
            ->method('getRelatedActivityId')
            ->willReturn(1);

        $this->provider = new TaskActivityListProvider(
            $this->doctrineHelper,
            $this->entityOwnerAccessorLink,
            $this->activityAssociationHelper,
            $this->commentAssociationHelper
        );

        $this->task = new Task();
    }

    public function testGetProperties()
    {
        $this->task->setSubject('Test subject');
        $this->task->setDescription('Tets description');
        $this->task->setOwner(new User());
        $this->task->setCreatedAt(new \DateTime());
        $this->task->setUpdatedAt(new \DateTime());
        $this->task->setOrganization(new Organization());

        self::assertEquals($this->task->getSubject(), $this->provider->getSubject($this->task));
        self::assertEquals($this->task->getDescription(), $this->provider->getDescription($this->task));
        self::assertEquals($this->task->getOwner(), $this->provider->getOwner($this->task));
        self::assertEquals($this->task->getOrganization(), $this->provider->getOrganization($this->task));
        self::assertEquals($this->task->getCreatedAt(), $this->provider->getCreatedAt($this->task));
        self::assertEquals($this->task->getUpdatedAt(), $this->provider->getUpdatedAt($this->task));
        self::assertEquals($this->task->getActivityTargets(), $this->provider->getTargetEntities($this->task));
    }

    public function testIsApplicableTarget()
    {
        $this->activityAssociationHelper->expects($this->once())
            ->method('isActivityAssociationEnabled')
            ->with(Task::class, Task::class, true)
            ->willReturn(true);

        self::assertTrue($this->provider->isApplicableTarget(Task::class, true));
    }

    public function testGetData()
    {
        $status = new TestEnumValue('open', 'Open', 1);
        $task = new TaskStub();
        $task->setStatus($status);

        $taskRepository = $this->createMock(EntityRepository::class);
        $taskRepository->expects($this->any())
            ->method('find')
            ->with(1)
            ->willReturn($task);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->with(Task::class)
            ->willReturn($taskRepository);

        $this->doctrineHelper->expects($this->any())
            ->method('getEntityManager')
            ->with(Task::class)
            ->willReturn($entityManager);

        $expected =  ['statusId' => $task->getStatus()->getId(), 'statusName' => $task->getStatus()->getName()];
        self::assertEquals($expected, $this->provider->getData($this->activityList));
    }

    public function testGetActivityId()
    {
        $this->doctrineHelper->expects($this->any())
            ->method('getSingleEntityIdentifier')
            ->with($this->task)
            ->willReturn(null);

        self::assertNull($this->provider->getActivityId($this->task));
    }

    public function testIsApplicable()
    {
        self::assertTrue($this->provider->isApplicable($this->task));
        self::assertTrue($this->provider->isApplicable(Task::class));
        self::assertFalse($this->provider->isApplicable(new \stdClass()));
        self::assertFalse($this->provider->isApplicable(\stdClass::class));
    }

    public function testIsCommentsEnabled()
    {
        $this->commentAssociationHelper->expects($this->once())
            ->method('isCommentAssociationEnabled')
            ->with(Task::class)
            ->willReturn(true);

        self::assertTrue($this->provider->isCommentsEnabled(Task::class));
    }

    public function testGetActivityOwners()
    {
        $this->task->setOwner(new User());
        $this->entityOwnerAccessorLink->expects($this->any())
            ->method('getService')
            ->willReturn($this->provider);

        $this->task->setOrganization(new Organization());
        $activityOwner = $this->provider->getActivityOwners($this->task, $this->activityList);
        self::assertIsArray($activityOwner);
        self::assertCount(1, $activityOwner);
        self::assertEquals($this->task->getOwner(), $activityOwner[0]->getUser());
    }
}
