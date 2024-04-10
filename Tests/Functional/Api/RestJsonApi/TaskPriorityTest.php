<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;

class TaskPriorityTest extends RestJsonApiTestCase
{
    public function testGetList(): void
    {
        $response = $this->cget(['entity' => 'taskpriorities']);
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'taskpriorities',
                        'id'         => 'high',
                        'attributes' => [
                            'label' => 'High',
                            'order' => 3
                        ]
                    ],
                    [
                        'type'       => 'taskpriorities',
                        'id'         => 'low',
                        'attributes' => [
                            'label' => 'Low',
                            'order' => 1
                        ]
                    ],
                    [
                        'type'       => 'taskpriorities',
                        'id'         => 'normal',
                        'attributes' => [
                            'label' => 'Normal',
                            'order' => 2
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetListFilteredAndSortedByOrder(): void
    {
        $response = $this->cget(
            ['entity' => 'taskpriorities'],
            ['filter[order]' => '1..2', 'sort' => '-order']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'taskpriorities',
                        'id'         => 'normal',
                        'attributes' => [
                            'label' => 'Normal',
                            'order' => 2
                        ]
                    ],
                    [
                        'type'       => 'taskpriorities',
                        'id'         => 'low',
                        'attributes' => [
                            'label' => 'Low',
                            'order' => 1
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGet(): void
    {
        $response = $this->get(['entity' => 'taskpriorities', 'id' => 'normal']);
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'taskpriorities',
                    'id'         => 'normal',
                    'attributes' => [
                        'label' => 'Normal',
                        'order' => 2
                    ]
                ]
            ],
            $response
        );
    }

    public function testTryToCreate(): void
    {
        $response = $this->post(
            ['entity' => 'taskpriorities', 'id' => 'new_status'],
            ['data' => ['type' => 'taskpriorities', 'id' => 'new_status']],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToDelete(): void
    {
        $response = $this->delete(
            ['entity' => 'taskpriorities', 'id' => 'normal'],
            [],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToDeleteList(): void
    {
        $response = $this->cdelete(
            ['entity' => 'taskpriorities'],
            ['filter[id]' => 'normal'],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testGetOptionsForList(): void
    {
        $response = $this->options(
            $this->getListRouteName(),
            ['entity' => 'taskpriorities']
        );
        self::assertAllowResponseHeader($response, 'OPTIONS, GET');
    }

    public function testOptionsForItem(): void
    {
        $response = $this->options(
            $this->getItemRouteName(),
            ['entity' => 'taskpriorities', 'id' => 'normal']
        );
        self::assertAllowResponseHeader($response, 'OPTIONS, GET');
    }
}
