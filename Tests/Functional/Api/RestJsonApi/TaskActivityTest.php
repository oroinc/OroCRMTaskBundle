<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ActivityBundle\EntityConfig\ActivityScope;
use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\TaskBundle\Entity\Task;

class TaskActivityTest extends RestJsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            '@OroTaskBundle/Tests/Functional/Api/DataFixtures/task_data.yml'
        ]);
    }

    private function getActivityTaskIds(int $contactId): array
    {
        $rows = $this->getEntityManager()->createQueryBuilder()
            ->from(Task::class, 't')
            ->select('t.id')
            ->join('t.' . ExtendHelper::buildAssociationName(Contact::class, ActivityScope::ASSOCIATION_KIND), 'c')
            ->where('c.id = :contactId')
            ->setParameter('contactId', $contactId)
            ->orderBy('t.id')
            ->getQuery()
            ->getArrayResult();

        return array_column($rows, 'id');
    }

    public function testGet(): void
    {
        $response = $this->get(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'          => 'contacts',
                    'id'            => '<toString(@contact1->id)>',
                    'relationships' => [
                        'activityTasks' => [
                            'data' => [
                                ['type' => 'tasks', 'id' => '<toString(@task1->id)>'],
                                ['type' => 'tasks', 'id' => '<toString(@task2->id)>']
                            ]
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetSubresourceForActivityTasks(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>', 'association' => 'activityTasks']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task1->id)>',
                        'attributes' => [
                            'subject' => '<toString(@task1->subject)>'
                        ]
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task2->id)>',
                        'attributes' => [
                            'subject' => '<toString(@task2->subject)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
    }

    public function testGetSubresourceForActivityTasksWithIncludeFilter(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>', 'association' => 'activityTasks'],
            ['include' => 'status']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task1->id)>',
                        'attributes' => [
                            'subject' => '<toString(@task1->subject)>'
                        ]
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task2->id)>',
                        'attributes' => [
                            'subject' => '<toString(@task2->subject)>'
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'taskstatuses',
                        'id'         => '<toString(@task_status_open->id)>',
                        'attributes' => [
                            'name' => '<toString(@task_status_open->name)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
    }

    public function testGetRelationshipForActivityTasks(): void
    {
        $response = $this->getRelationship(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>', 'association' => 'activityTasks']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    ['type' => 'tasks', 'id' => '<toString(@task1->id)>'],
                    ['type' => 'tasks', 'id' => '<toString(@task2->id)>']
                ]
            ],
            $response,
            true
        );
    }

    public function testUpdateRelationshipForActivityTasks(): void
    {
        $contactId = $this->getReference('contact2')->getId();
        $task2Id = $this->getReference('task2')->getId();
        $this->patchRelationship(
            ['entity' => 'contacts', 'id' => (string)$contactId, 'association' => 'activityTasks'],
            [
                'data' => [
                    ['type' => 'tasks', 'id' => (string)$task2Id]
                ]
            ]
        );
        self::assertEquals([$task2Id], $this->getActivityTaskIds($contactId));
    }

    public function testAddRelationshipForActivityTasks(): void
    {
        $contactId = $this->getReference('contact2')->getId();
        $task2Id = $this->getReference('task2')->getId();
        $task3Id = $this->getReference('task3')->getId();
        $this->postRelationship(
            ['entity' => 'contacts', 'id' => (string)$contactId, 'association' => 'activityTasks'],
            [
                'data' => [
                    ['type' => 'tasks', 'id' => (string)$task3Id]
                ]
            ]
        );
        self::assertEquals([$task2Id, $task3Id], $this->getActivityTaskIds($contactId));
    }

    public function testDeleteRelationshipForActivityTasks(): void
    {
        $contactId = $this->getReference('contact1')->getId();
        $task1Id = $this->getReference('task1')->getId();
        $task2Id = $this->getReference('task2')->getId();
        $this->deleteRelationship(
            ['entity' => 'contacts', 'id' => (string)$contactId, 'association' => 'activityTasks'],
            [
                'data' => [
                    ['type' => 'tasks', 'id' => (string)$task1Id]
                ]
            ]
        );
        self::assertEquals([$task2Id], $this->getActivityTaskIds($contactId));
    }
}
