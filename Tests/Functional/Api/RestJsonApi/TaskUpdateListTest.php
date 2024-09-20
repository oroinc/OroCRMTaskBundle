<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiUpdateListTestCase;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\TaskBundle\Entity\Task;

/**
 * @dbIsolationPerTest
 */
class TaskUpdateListTest extends RestJsonApiUpdateListTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures(['@OroTaskBundle/Tests/Functional/Api/DataFixtures/task_data.yml']);
    }

    public function testCreateEntities(): void
    {
        $this->processUpdateList(
            Task::class,
            [
                'data' => [
                    [
                        'type'          => 'tasks',
                        'attributes'    => ['subject' => 'New Task 1'],
                        'relationships' => [
                            'taskPriority' => [
                                'data' => [
                                    'type' => 'taskpriorities',
                                    'id'   => '<toString(@task_priority_normal->name)>'
                                ]
                            ],
                            'status'       => [
                                'data' => [
                                    'type' => 'taskstatuses',
                                    'id'   => '<toString(@task_status_open->internalId)>'
                                ]
                            ]
                        ]
                    ],
                    [
                        'type'          => 'tasks',
                        'attributes'    => ['subject' => 'New Task 2'],
                        'relationships' => [
                            'taskPriority' => [
                                'data' => [
                                    'type' => 'taskpriorities',
                                    'id'   => '<toString(@task_priority_normal->name)>'
                                ]
                            ],
                            'status'       => [
                                'data' => [
                                    'type' => 'taskstatuses',
                                    'id'   => '<toString(@task_status_open->internalId)>'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $response = $this->cget(['entity' => 'tasks'], ['fields[tasks]' => 'subject']);
        $responseContent = $this->updateResponseContent(
            [
                'data' => [
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task1->id)>',
                        'attributes' => ['subject' => 'Meet James']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task2->id)>',
                        'attributes' => ['subject' => 'Task 2']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task3->id)>',
                        'attributes' => ['subject' => 'Task 3']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => 'new',
                        'attributes' => ['subject' => 'New Task 1']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => 'new',
                        'attributes' => ['subject' => 'New Task 2']
                    ]
                ]
            ],
            $response
        );
        $this->assertResponseContains($responseContent, $response);
    }

    public function testUpdateEntities(): void
    {
        $this->processUpdateList(
            Task::class,
            [
                'data' => [
                    [
                        'meta'       => ['update' => true],
                        'type'       => 'tasks',
                        'id'         => '<toString(@task1->id)>',
                        'attributes' => ['subject' => 'Updated Task 1']
                    ],
                    [
                        'meta'       => ['update' => true],
                        'type'       => 'tasks',
                        'id'         => '<toString(@task2->id)>',
                        'attributes' => ['subject' => 'Updated Task 2']
                    ]
                ]
            ]
        );

        $response = $this->cget(['entity' => 'tasks'], ['fields[tasks]' => 'subject']);
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task1->id)>',
                        'attributes' => ['subject' => 'Updated Task 1']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task2->id)>',
                        'attributes' => ['subject' => 'Updated Task 2']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task3->id)>',
                        'attributes' => ['subject' => 'Task 3']
                    ]
                ]
            ],
            $response
        );
    }

    public function testCreateAndUpdateEntities(): void
    {
        $this->processUpdateList(
            Task::class,
            [
                'data' => [
                    [
                        'type'          => 'tasks',
                        'attributes'    => ['subject' => 'New Task 1'],
                        'relationships' => [
                            'taskPriority' => [
                                'data' => [
                                    'type' => 'taskpriorities',
                                    'id'   => '<toString(@task_priority_normal->name)>'
                                ]
                            ],
                            'status'       => [
                                'data' => [
                                    'type' => 'taskstatuses',
                                    'id'   => '<toString(@task_status_open->internalId)>'
                                ]
                            ]
                        ]
                    ],
                    [
                        'meta'       => ['update' => true],
                        'type'       => 'tasks',
                        'id'         => '<toString(@task1->id)>',
                        'attributes' => ['subject' => 'Updated Task 1']
                    ]
                ]
            ]
        );

        $response = $this->cget(['entity' => 'tasks'], ['fields[tasks]' => 'subject']);
        $responseContent = $this->updateResponseContent(
            [
                'data' => [
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task1->id)>',
                        'attributes' => ['subject' => 'Updated Task 1']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task2->id)>',
                        'attributes' => ['subject' => 'Task 2']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => '<toString(@task3->id)>',
                        'attributes' => ['subject' => 'Task 3']
                    ],
                    [
                        'type'       => 'tasks',
                        'id'         => 'new',
                        'attributes' => ['subject' => 'New Task 1']
                    ]
                ]
            ],
            $response
        );
        $this->assertResponseContains($responseContent, $response);
    }

    public function testCreateEntitiesWithIncludes(): void
    {
        $this->processUpdateList(
            Task::class,
            [
                'data'     => [
                    [
                        'type'          => 'tasks',
                        'attributes'    => ['subject' => 'New Task 1'],
                        'relationships' => [
                            'taskPriority'    => [
                                'data' => [
                                    'type' => 'taskpriorities',
                                    'id'   => '<toString(@task_priority_normal->name)>'
                                ]
                            ],
                            'status'          => [
                                'data' => [
                                    'type' => 'taskstatuses',
                                    'id'   => '<toString(@task_status_open->internalId)>'
                                ]
                            ],
                            'activityTargets' => ['data' => [['type' => 'contacts', 'id' => 'c1']]]
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'contacts',
                        'id'         => 'c1',
                        'attributes' => ['firstName' => 'Included contact 1']
                    ]
                ]
            ]
        );

        $response = $this->cget(
            ['entity' => 'tasks'],
            [
                'filter[id][gt]' => '@task3->id',
                'fields[tasks]'  => 'subject,activityTargets'
            ]
        );
        $responseContent = $this->updateResponseContent(
            [
                'data' => [
                    [
                        'type'          => 'tasks',
                        'id'            => 'new',
                        'attributes'    => ['subject' => 'New Task 1'],
                        'relationships' => [
                            'activityTargets' => ['data' => [['type' => 'contacts', 'id' => 'new']]]
                        ]
                    ]
                ]
            ],
            $response
        );
        $this->assertResponseContains($responseContent, $response);

        /** @var Task $task1 */
        $task1 = $this->getEntityManager()->getRepository(Task::class)->findOneBy(['subject' => 'New Task 1']);
        /** @var Contact $relatedContact */
        $relatedContact = $task1->getActivityTargets(Contact::class)[0];
        self::assertEquals('Included contact 1', $relatedContact->getFirstName());
    }

    public function testTryToCreateEntitiesWithErrorsInIncludes(): void
    {
        $operationId = $this->processUpdateList(
            Task::class,
            [
                'data'     => [
                    [
                        'type'          => 'tasks',
                        'attributes'    => ['subject' => 'New Task 1'],
                        'relationships' => [
                            'taskPriority'    => [
                                'data' => [
                                    'type' => 'taskpriorities',
                                    'id'   => '<toString(@task_priority_normal->name)>'
                                ]
                            ],
                            'status'          => [
                                'data' => [
                                    'type' => 'taskstatuses',
                                    'id'   => '<toString(@task_status_open->internalId)>'
                                ]
                            ],
                            'activityTargets' => ['data' => [['type' => 'contacts', 'id' => 'c1']]]
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'contacts',
                        'id'         => 'c1',
                        'attributes' => ['birthday' => 'Invalid Value']
                    ]
                ]
            ],
            false
        );

        $this->assertAsyncOperationErrors(
            [
                [
                    'id'     => $operationId . '-1-1',
                    'status' => 400,
                    'title'  => 'has contact information constraint',
                    'detail' => 'At least one of the fields First name, Last name, Emails or Phones must be defined.',
                    'source' => ['pointer' => '/included/0']
                ],
                [
                    'id'     => $operationId . '-1-2',
                    'status' => 400,
                    'title'  => 'form constraint',
                    'detail' => 'The "Invalid Value" is not valid date.',
                    'source' => ['pointer' => '/included/0/attributes/birthday']
                ]
            ],
            $operationId
        );
    }
}
