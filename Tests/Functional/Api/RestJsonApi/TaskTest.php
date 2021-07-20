<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Api;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Tests\Functional\DataFixtures\LoadTaskPriorityData;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;
use Oro\Bundle\UserProBundle\Tests\Functional\DataFixtures\LoadOrganizationData;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class TaskTest extends RestJsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $fixtures = [
            '@OroTaskBundle/Tests/Functional/Api/DataFixtures/task_data.yml'
        ];
        // load several organizations for EE platform
        if (class_exists(LoadOrganizationData::class)) {
            $fixtures[] = LoadOrganizationData::class;
        }
        $this->loadFixtures($fixtures);
    }

    private function getActivityTargetIds(Task $task, string $targetClass): array
    {
        $result = [];
        $targets = $task->getActivityTargets($targetClass);
        foreach ($targets as $target) {
            $result[] = $target->getId();
        }
        sort($result);

        return $result;
    }

    public function testGetList()
    {
        $response = $this->cget(['entity' => 'tasks']);
        $this->assertResponseContains('cget_task.yml', $response);
    }

    public function testGetListFilteredByOwnerUsername()
    {
        $response = $this->cget(
            ['entity' => 'tasks'],
            ['filter[ownerUsername]' => 'user1']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    ['type' => 'tasks', 'id' => '<toString(@task2->id)>']
                ]
            ],
            $response
        );
    }

    public function testGetListWithActivityTargetsInIncludeFilter()
    {
        $response = $this->cget(
            ['entity' => 'tasks'],
            ['include' => 'activityTargets,activityTargets.organization']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    [
                        'type'          => 'tasks',
                        'id'            => '<toString(@task1->id)>',
                        'relationships' => [
                            'activityTargets' => [
                                'data' => [
                                    ['type' => 'contacts', 'id' => '<toString(@contact1->id)>']
                                ]
                            ]
                        ]
                    ],
                    [
                        'type'          => 'tasks',
                        'id'            => '<toString(@task2->id)>',
                        'relationships' => [
                            'activityTargets' => [
                                'data' => []
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'organizations',
                        'id'         => '<toString(@organization->id)>',
                        'attributes' => [
                            'name' => '<toString(@organization->name)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact1->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact1->firstName)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact2->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact2->firstName)>'
                        ]
                    ]
                ]
            ],
            $response
        );
        $responseContent = self::jsonToArray($response->getContent());
        self::assertCount(3, $responseContent['included'], 'included');
        foreach ($responseContent['included'] as $key => $item) {
            self::assertArrayNotHasKey('meta', $responseContent['included'][$key], sprintf('included[%s]', $key));
        }
    }

    public function testGetListActivityTargetsInIncludeFilterAndTitle()
    {
        $response = $this->cget(
            ['entity' => 'tasks'],
            ['include' => 'activityTargets,activityTargets.organization', 'meta' => 'title']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    [
                        'type'          => 'tasks',
                        'id'            => '<toString(@task1->id)>',
                        'meta'          => [
                            'title' => 'Meet James'
                        ],
                        'relationships' => [
                            'activityTargets' => [
                                'data' => [
                                    ['type' => 'contacts', 'id' => '<toString(@contact1->id)>']
                                ]
                            ]
                        ]
                    ],
                    [
                        'type'          => 'tasks',
                        'id'            => '<toString(@task2->id)>',
                        'meta'          => [
                            'title' => 'Task 2'
                        ],
                        'relationships' => [
                            'activityTargets' => [
                                'data' => []
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'organizations',
                        'id'         => '<toString(@organization->id)>',
                        'meta'       => [
                            'title' => '<toString(@organization->name)>'
                        ],
                        'attributes' => [
                            'name' => '<toString(@organization->name)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact1->id)>',
                        'meta'       => [
                            'title' => '<toString(@contact1->firstName)>'
                        ],
                        'attributes' => [
                            'firstName' => '<toString(@contact1->firstName)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact2->id)>',
                        'meta'       => [
                            'title' => '<toString(@contact2->firstName)>'
                        ],
                        'attributes' => [
                            'firstName' => '<toString(@contact2->firstName)>'
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGet()
    {
        $response = $this->get(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>']
        );
        $this->assertResponseContains('get_task.yml', $response);
    }

    public function testCreate()
    {
        $contact1Id = $this->getReference('contact1')->getId();

        $response = $this->post(
            ['entity' => 'tasks'],
            'create_task.yml'
        );

        $taskId = (int)$this->getResourceId($response);
        /** @var Task $task */
        $task = $this->getEntityManager()->find(Task::class, $taskId);
        self::assertEquals('Subject of test task', $task->getSubject());
        self::assertEquals('Description of test task', $task->getDescription());
        self::assertEquals(new \DateTime('2035-02-16T22:36:37Z'), $task->getDueDate());
        self::assertEquals('Normal', $task->getTaskPriority()->getLabel());
        self::assertEquals('Open', $task->getStatus()->getName());
        self::assertEquals([$contact1Id], $this->getActivityTargetIds($task, Contact::class));
    }

    public function testUpdate()
    {
        $taskId = $this->getReference('task1')->getId();
        $response = $this->patch(
            ['entity' => 'tasks', 'id' => (string)$taskId],
            'update_task.yml'
        );
        $this->assertResponseContains('update_task.yml', $response);

        /** @var Task $task */
        $task = $this->getEntityManager()->find(Task::class, $taskId);
        self::assertEquals('New subject of test task', $task->getSubject());
        self::assertEquals('New description of test task', $task->getDescription());
        self::assertEquals(new \DateTime('2036-02-16T22:36:37Z'), $task->getDueDate());
        self::assertEquals('High', $task->getTaskPriority()->getLabel());
        self::assertEquals('Open', $task->getStatus()->getName());
    }

    public function testGetSubresourceForStatus()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'status']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'taskstatuses',
                    'id'         => '<toString(@task_status_open->id)>',
                    'attributes' => [
                        'name' => '<toString(@task_status_open->name)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForStatus()
    {
        $response = $this->getRelationship(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'status']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'taskstatuses',
                    'id'   => '<toString(@task_status_open->id)>'
                ]
            ],
            $response
        );
    }

    public function testTryToUpdateRelationshipForStatus()
    {
        $taskId = $this->getReference('task1')->getId();
        $response = $this->patchRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'status'],
            [
                'data' => [
                    'type' => 'taskstatuses',
                    'id'   => '<toString(@task_status_in_progress->id)>'
                ]
            ],
            [],
            false
        );
        $this->assertResponseValidationError(
            [
                'title'  => 'workflow entity constraint',
                'detail' => 'Field could not be edited because of workflow restrictions.'
            ],
            $response
        );
    }

    public function testGetSubresourceForOwner()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'owner']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'users',
                    'id'         => '<toString(@task1->owner->id)>',
                    'attributes' => [
                        'username' => '<toString(@task1->owner->username)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForOwner()
    {
        $response = $this->getRelationship(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'owner']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'users',
                    'id'   => '<toString(@task1->owner->id)>'
                ]
            ],
            $response
        );
    }

    public function testUpdateRelationshipForOwner()
    {
        $taskId = $this->getReference('task1')->getId();
        $ownerId = $this->getReference(LoadUserData::SIMPLE_USER_2)->getId();
        $this->patchRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'owner'],
            [
                'data' => [
                    'type' => 'users',
                    'id'   => (string)$ownerId
                ]
            ]
        );
        /** @var Task $task */
        $task = $this->getEntityManager()->find(Task::class, $taskId);
        self::assertSame($ownerId, $task->getOwner()->getId());
    }

    public function testGetSubresourceForOrganization()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'organization']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'organizations',
                    'id'         => '<toString(@task1->organization->id)>',
                    'attributes' => [
                        'name' => '<toString(@task1->organization->name)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForOrganization()
    {
        $response = $this->getRelationship(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'organization']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'organizations',
                    'id'   => '<toString(@task1->organization->id)>'
                ]
            ],
            $response
        );
    }

    public function testTryToUpdateRelationshipForOrganization()
    {
        if (!class_exists(LoadOrganizationData::class)) {
            $this->markTestSkipped('EE platform is required');
        }

        $taskId = $this->getReference('task1')->getId();
        $response = $this->patchRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'organization'],
            [
                'data' => [
                    'type' => 'organizations',
                    'id'   => '<toString(@organization_1->id)>'
                ]
            ],
            [],
            false
        );
        $this->assertResponseContainsValidationError(
            [
                'title'  => 'organization constraint',
                'detail' => 'You have no access to set this value as organization.'
            ],
            $response
        );
        $this->assertResponseContainsValidationError(
            [
                'title'  => 'access granted constraint',
                'detail' => 'The "VIEW" permission is denied for the related resource.'
            ],
            $response
        );
    }

    public function testGetSubresourceForTaskPriority()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'taskPriority']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'taskpriorities',
                    'id'         => '<toString(@task_priority_high->name)>',
                    'attributes' => [
                        'label' => '<toString(@task_priority_high->label)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForTaskPriority()
    {
        $response = $this->getRelationship(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'taskPriority']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'taskpriorities',
                    'id'   => '<toString(@task_priority_high->name)>'
                ]
            ],
            $response
        );
    }

    public function testUpdateRelationshipForTaskPriority()
    {
        $taskId = $this->getReference('task1')->getId();
        $priorityName = $this->getReference(LoadTaskPriorityData::TASK_PRIORITY_LOW)->getName();
        $this->patchRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'taskPriority'],
            [
                'data' => [
                    'type' => 'taskpriorities',
                    'id'   => $priorityName
                ]
            ]
        );
        /** @var Task $task */
        $task = $this->getEntityManager()->find(Task::class, $taskId);
        self::assertSame($priorityName, $task->getTaskPriority()->getName());
    }

    public function testGetSubresourceForCreatedBy()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'createdBy']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'users',
                    'id'         => '<toString(@task1->createdBy->id)>',
                    'attributes' => [
                        'username' => '<toString(@task1->createdBy->username)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForCreatedBy()
    {
        $response = $this->getRelationship(
            ['entity' => 'tasks', 'id' => '<toString(@task1->id)>', 'association' => 'createdBy']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'users',
                    'id'   => '<toString(@task1->createdBy->id)>'
                ]
            ],
            $response
        );
    }

    public function testTryToUpdateRelationshipForCreatedBy()
    {
        $taskId = $this->getReference('task1')->getId();
        $response = $this->patchRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'createdBy'],
            [
                'data' => [
                    'type' => 'users',
                    'id'   => '<toString(@simple_user2->id)>'
                ]
            ],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testGetSubresourceForActivityTargets()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task2->id)>', 'association' => 'activityTargets']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact1->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact1->firstName)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact2->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact2->firstName)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
        $responseContent = self::jsonToArray($response->getContent());
        foreach ($responseContent['data'] as $key => $item) {
            self::assertArrayNotHasKey('meta', $item, sprintf('data[%s]', $key));
        }
    }

    public function testGetSubresourceForActivityTargetsWithIncludeFilter()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task2->id)>', 'association' => 'activityTargets'],
            ['include' => 'organization']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact1->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact1->firstName)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact2->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact2->firstName)>'
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'organizations',
                        'id'         => '<toString(@organization->id)>',
                        'attributes' => [
                            'name' => '<toString(@organization->name)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
        $responseContent = self::jsonToArray($response->getContent());
        foreach ($responseContent['data'] as $key => $item) {
            self::assertArrayNotHasKey('meta', $item, sprintf('data[%s]', $key));
        }
        self::assertCount(1, $responseContent['included']);
        foreach ($responseContent['included'] as $key => $item) {
            self::assertArrayNotHasKey('meta', $item, sprintf('included[%s]', $key));
        }
    }

    public function testGetSubresourceForActivityTargetsWithIncludeFilterAndTitle()
    {
        $response = $this->getSubresource(
            ['entity' => 'tasks', 'id' => '<toString(@task2->id)>', 'association' => 'activityTargets'],
            ['include' => 'organization', 'meta' => 'title']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact1->id)>',
                        'meta'       => [
                            'title' => '<toString(@contact1->firstName)>'
                        ],
                        'attributes' => [
                            'firstName' => '<toString(@contact1->firstName)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact2->id)>',
                        'meta'       => [
                            'title' => '<toString(@contact2->firstName)>'
                        ],
                        'attributes' => [
                            'firstName' => '<toString(@contact2->firstName)>'
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'organizations',
                        'id'         => '<toString(@organization->id)>',
                        'meta'       => [
                            'title' => '<toString(@organization->name)>'
                        ],
                        'attributes' => [
                            'name' => '<toString(@organization->name)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
    }

    public function testGetRelationshipForActivityTargets()
    {
        $response = $this->getRelationship(
            ['entity' => 'tasks', 'id' => '<toString(@task2->id)>', 'association' => 'activityTargets']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    ['type' => 'contacts', 'id' => '<toString(@contact1->id)>'],
                    ['type' => 'contacts', 'id' => '<toString(@contact2->id)>']
                ]
            ],
            $response,
            true
        );
        $responseContent = self::jsonToArray($response->getContent());
        foreach ($responseContent['data'] as $key => $item) {
            self::assertArrayNotHasKey('meta', $item, sprintf('data[%s]', $key));
            self::assertArrayNotHasKey('attributes', $item, sprintf('data[%s]', $key));
            self::assertArrayNotHasKey('relationships', $item, sprintf('data[%s]', $key));
        }
    }

    public function testUpdateRelationshipForActivityTargets()
    {
        $taskId = $this->getReference('task1')->getId();
        $contact2Id = $this->getReference('contact2')->getId();
        $this->patchRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'activityTargets'],
            [
                'data' => [
                    ['type' => 'contacts', 'id' => (string)$contact2Id]
                ]
            ]
        );
        /** @var Task $task */
        $task = $this->getEntityManager()->find(Task::class, $taskId);
        self::assertEquals([$contact2Id], $this->getActivityTargetIds($task, Contact::class));
    }

    public function testAddRelationshipForActivityTargets()
    {
        $taskId = $this->getReference('task1')->getId();
        $contact1Id = $this->getReference('contact1')->getId();
        $contact2Id = $this->getReference('contact2')->getId();
        $this->postRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'activityTargets'],
            [
                'data' => [
                    ['type' => 'contacts', 'id' => (string)$contact1Id]
                ]
            ]
        );
        /** @var Task $task */
        $task = $this->getEntityManager()->find(Task::class, $taskId);
        self::assertEquals([$contact1Id, $contact2Id], $this->getActivityTargetIds($task, Contact::class));
    }

    public function testDeleteRelationshipForActivityTargets()
    {
        $taskId = $this->getReference('task1')->getId();
        $contact1Id = $this->getReference('contact1')->getId();
        $contact2Id = $this->getReference('contact2')->getId();
        $this->deleteRelationship(
            ['entity' => 'tasks', 'id' => (string)$taskId, 'association' => 'activityTargets'],
            [
                'data' => [
                    ['type' => 'contacts', 'id' => (string)$contact1Id]
                ]
            ]
        );
        /** @var Task $task */
        $task = $this->getEntityManager()->find(Task::class, $taskId);
        self::assertEquals([$contact2Id], $this->getActivityTargetIds($task, Contact::class));
    }

    public function testDelete()
    {
        $taskId = $this->getReference('task1')->getId();
        $this->delete(
            ['entity' => 'tasks', 'id' => (string)$taskId]
        );
        self::assertTrue(null === $this->getEntityManager()->find(Task::class, $taskId));
    }
}
