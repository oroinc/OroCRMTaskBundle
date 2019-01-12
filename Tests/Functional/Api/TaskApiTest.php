<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Api;

use Doctrine\Common\Collections\Collection;
use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures\LoadTaskPriorityData;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;
use Oro\Bundle\UserProBundle\Tests\Functional\DataFixtures\LoadOrganizationData;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class TaskApiTest extends RestJsonApiTestCase
{
    private const LOAD_ORGANIZATION_FIXTURE_CLASS = LoadOrganizationData::class;

    /**
     * @var Task $task
     */
    protected $task;

    protected function setUp()
    {
        $fixtures = [
            '@OroTaskBundle/Tests/Functional/Api/DataFixtures/task_data.yml'
        ];

        if (class_exists(self::LOAD_ORGANIZATION_FIXTURE_CLASS)) {
            $fixtures[] = self::LOAD_ORGANIZATION_FIXTURE_CLASS;
        }

        parent::setUp();
        $this->loadFixtures($fixtures);

        $this->task = $this->getReference('reference_task1');
    }

    public function testGetList()
    {
        $response = $this->cget(['entity' => 'tasks'], []);
        $this->assertResponseContains('task_cget.yml', $response);
    }

    public function testCreate()
    {
        $this->post(
            ['entity' => 'tasks'],
            'task_create.yml'
        );

        $task = $this->getTaskBy(['subject' => 'Subject of test task']);

        self::assertEquals('Subject of test task', $task->getSubject());
        self::assertEquals('Description of test task', $task->getDescription());
        self::assertEquals(new \DateTime('2035-02-16T22:36:37Z'), $task->getDueDate());
        self::assertEquals('Normal', $task->getTaskPriority()->getLabel());
        self::assertEquals('Open', $task->getStatus()->getName());

        /** @var Collection $contactTargets */
        $contactTargets = $task->getActivityTargets(Contact::class);

        /** @var Contact[] $contacts */
        $contacts = $contactTargets->toArray();

        self::assertCount(1, $contacts);

        /** @var Contact $actualContact */
        $actualContact = reset($contacts);

        /** @var Contact $expectedContact */
        $expectedContact = $this->getReference('contact1');
        self::assertSame($expectedContact->getId(), $actualContact->getId());

        $this->removeTask($task);
    }

    public function testGet()
    {
        $response = $this->get(['entity' => 'tasks', 'id' => $this->task->getId()]);
        $this->assertResponseContains('task_get.yml', $response);
    }

    public function testUpdate()
    {
        $response = $this->patch(
            ['entity' => 'tasks', 'id' => (string)$this->task->getId()],
            'task_update.yml'
        );
        $this->assertResponseContains('task_patch.yml', $response);

        /** @var Task $task */
        $task = $this->getTaskBy(['id' => $this->task->getId()]);
        self::assertEquals('New subject of test task', $task->getSubject());
        self::assertEquals('New description of test task', $task->getDescription());
        self::assertEquals(new \DateTime('2036-02-16T22:36:37Z'), $task->getDueDate());
        self::assertEquals('High', $task->getTaskPriority()->getLabel());
        self::assertEquals('Open', $task->getStatus()->getName());
    }

    public function testGetRelationship()
    {
        $this->assertGetRelationShip(
            $this->task->getId(),
            'status',
            'relationship/task_relation_status_get.yml'
        );
        $this->assertGetRelationShip(
            $this->task->getId(),
            'owner',
            'relationship/task_relation_owner_get.yml'
        );
        $this->assertGetRelationShip(
            $this->task->getId(),
            'organization',
            'relationship/task_relation_organization_get.yml'
        );
        $this->assertGetRelationShip(
            $this->task->getId(),
            'taskPriority',
            'relationship/task_relation_priority_get.yml'
        );
        $this->assertGetRelationShip(
            $this->task->getId(),
            'createdBy',
            'relationship/task_relation_created_by_get.yml'
        );
        $this->assertGetRelationShip(
            $this->task->getId(),
            'activityTargets',
            'relationship/task_relation_activity_get.yml'
        );
    }

    public function testPatchOwnerRelation()
    {
        $user = $this->getReference(LoadUserData::SIMPLE_USER_2);
        $this->assertPatchRelationShip($this->task->getId(), 'owner', 'relationship/test_relation_owner_patch.yml');
        $task = $this->getTaskBy(['owner' => $user]);
        self::assertEquals($user, $task->getOwner());
    }

    public function testPatchPriorityRelation()
    {
        $priority = $this->getReference(LoadTaskPriorityData::TASK_PRIORITY_HIGH);
        $this->assertPatchRelationShip(
            $this->task->getId(),
            'taskPriority',
            'relationship/task_relation_priority_patch.yml'
        );
        $task = $this->getTaskBy(['taskPriority' => $priority]);
        self::assertEquals($priority, $task->getTaskPriority());
    }

    public function testPatchOrganizationRelation()
    {
        if (!class_exists(self::LOAD_ORGANIZATION_FIXTURE_CLASS)) {
            $this->markTestSkipped('EE platform is required');
        }

        $organizationResponse = $this->assertPatchRelationShip(
            $this->task->getId(),
            'organization',
            'relationship/test_relation_organization_patch.yml',
            false
        );

        $this->assertResponseValidationError(
            [
                'status' => (string)Response::HTTP_BAD_REQUEST,
                'title' => 'organization constraint',
                'detail' => 'You have no access to set this value as organization.'
            ],
            $organizationResponse
        );
    }

    public function testPatchActivityRelation()
    {
        $this->assertPatchRelationShip(
            $this->task->getId(),
            'activityTargets',
            'relationship/task_relation_activity_patch.yml'
        );
        $expectedContact = $this->getReference('contact1');
        $task = $this->getTaskBy(['id' => $this->task->getId()]);

        /** @var Collection $contactTargets */
        $contactTargets = $task->getActivityTargets(Contact::class);

        /** @var Contact[] $contacts */
        $contacts = $contactTargets->toArray();

        /** @var Contact $actualContact */
        $actualContact = reset($contacts);
        self::assertSame($expectedContact->getId(), $actualContact->getId());
    }

    public function testPatchStatusRelation()
    {
        $statusResponse = $this->assertPatchRelationShip(
            $this->task->getId(),
            'status',
            'relationship/task_relation_status_patch.yml',
            false
        );

        $this->assertResponseValidationError(
            [
                'status' => (string)Response::HTTP_BAD_REQUEST,
                'title' => 'workflow entity constraint',
                'detail' => 'Field could not be edited because of workflow restrictions.'
            ],
            $statusResponse
        );
    }

    public function testGetSubresource()
    {
        $this->assertGetSubresource(
            $this->task->getId(),
            'activityTargets',
            'subresource/task_subresource_activity_get.yml'
        );
        $this->assertGetSubresource(
            $this->task->getId(),
            'status',
            'subresource/task_subresource_status_get.yml'
        );
        $this->assertGetSubresource(
            $this->task->getId(),
            'owner',
            'subresource/task_subresource_owner_get.yml'
        );
        $this->assertGetSubresource(
            $this->task->getId(),
            'organization',
            'subresource/task_subresource_organization_get.yml'
        );
        $this->assertGetSubresource(
            $this->task->getId(),
            'taskPriority',
            'subresource/task_subresource_priority_get.yml'
        );
        $this->assertGetSubresource(
            $this->task->getId(),
            'createdBy',
            'subresource/task_subresource_created_by_get.yml'
        );
    }

    public function testDelete()
    {
        $this->delete(['entity' => 'tasks', 'id' => (string)$this->task->getId()]);

        self::assertNull($this->getTaskBy(['id' => $this->task->getId()]));
    }

    /**
     * @param int    $entityId
     * @param string $associationName
     * @param string $expected
     *
     * @return Response
     */
    private function assertGetRelationShip(int $entityId, string $associationName, string $expected)
    {
        $response = $this->getRelationship(['id' => $entityId, 'entity' => 'tasks', 'association' => $associationName]);
        $this->assertResponseContains($expected, $response);

        return $response;
    }

    /**
     * @param int    $entityId
     * @param string $associationName
     * @param string $requestData
     * @param bool   $assertValid
     *
     * @return Response
     */
    private function assertPatchRelationShip(
        int $entityId,
        string $associationName,
        string $requestData,
        bool $assertValid = true
    ) {
        return $this->patchRelationship(
            ['entity' => 'tasks', 'id' => $entityId, 'association' => $associationName],
            $this->getRequestData($requestData),
            [],
            $assertValid
        );
    }

    /**
     * @param int    $entityId
     * @param string $associationName
     * @param string $expected
     *
     * @return Response
     */
    private function assertGetSubresource(int $entityId, string $associationName, string $expected)
    {
        $response = $this->getSubresource(['id' => $entityId, 'entity' => 'tasks', 'association' => $associationName]);
        $this->assertResponseContains($expected, $response);

        return $response;
    }

    /**
     * @param array $criteria
     *
     * @return null|Task
     */
    private function getTaskBy(array $criteria)
    {
        return $this->getEntityManager()->getRepository(Task::class)->findOneBy($criteria);
    }

    /**
     * @param Task $task
     */
    private function removeTask(Task $task)
    {
        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
}
