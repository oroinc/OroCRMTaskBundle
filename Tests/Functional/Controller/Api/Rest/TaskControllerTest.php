<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\TaskBundle\Controller\Api\Rest\TaskController;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    /** @var array */
    protected $task = [
        'subject'       => 'New task',
        'description'   => 'New description',
        'dueDate'       => '2014-03-04T20:00:00+0000',
        'taskPriority'  => 'high',
        'reminders'     =>  [
            [
                'method'    => 'email',
                'interval'  => [
                    'number'    =>  '10',
                    'unit'      =>  'M'
                ]
            ]
        ]
    ];

    protected function setUp(): void
    {
        $this->initClient([], $this->generateWsseAuthHeader());

        if (!isset($this->task['owner'])) {
            $this->task['owner'] = $this->getContainer()
                ->get('doctrine')
                ->getRepository('OroUserBundle:User')
                ->findOneBy(['username' => self::USER_NAME])->getId();
        }
    }

    /**
     * @return int
     */
    public function testCreate()
    {
        $this->client->request('POST', $this->getUrl('oro_api_post_task'), $this->task);
        $task = $this->getJsonResponseContent($this->client->getResponse(), 201);

        return $task['id'];
    }

    /**
     * @depends testCreate
     */
    public function testCget()
    {
        $this->client->request('GET', $this->getUrl('oro_api_get_tasks'));
        $tasks = $this->getJsonResponseContent($this->client->getResponse(), 200);

        self::assertCount(1, $tasks);
        $task = array_shift($tasks);

        self::assertEquals($this->task['subject'], $task['subject']);
        self::assertNotEmpty($task[TaskController::FIELD_WORKFLOW_ITEM]);
        self::assertNotEmpty($task[TaskController::FIELD_WORKFLOW_STEP]);
    }

    /**
     * @depends testCreate
     */
    public function testCgetFiltering()
    {
        $baseUrl = $this->getUrl('oro_api_get_tasks');

        $date     = '2014-03-04T20:00:00+0000';
        $ownerId  = $this->task['owner'];
        $randomId = rand($ownerId + 1, $ownerId + 100);

        $this->client->request('GET', $baseUrl . '?createdAt>' . $date);
        self::assertCount(1, $this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->request('GET', $baseUrl . '?createdAt<' . $date);
        self::assertEmpty($this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->request('GET', $baseUrl . '?ownerId=' . $ownerId);
        self::assertCount(1, $this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->request('GET', $baseUrl . '?ownerId=' . $randomId);
        self::assertEmpty($this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->request('GET', $baseUrl . '?ownerUsername=' . self::USER_NAME);
        self::assertCount(1, $this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->request('GET', $baseUrl . '?ownerUsername<>' . self::USER_NAME);
        self::assertEmpty($this->getJsonResponseContent($this->client->getResponse(), 200));
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testGet($id)
    {
        $this->client->request('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $task = $this->getJsonResponseContent($this->client->getResponse(), 200);

        self::assertEquals($this->task['subject'], $task['subject']);
        self::assertNotEmpty($task[TaskController::FIELD_WORKFLOW_ITEM]);
        self::assertNotEmpty($task[TaskController::FIELD_WORKFLOW_STEP]);
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testPut($id)
    {
        $updatedTask = array_merge($this->task, ['subject' => 'Updated subject']);
        $this->client->request('PUT', $this->getUrl('oro_api_put_task', ['id' => $id]), $updatedTask);
        $result = $this->client->getResponse();
        self::assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $task = $this->getJsonResponseContent($this->client->getResponse(), 200);

        self::assertEquals('Updated subject', $task['subject']);
        self::assertEquals($updatedTask['subject'], $task['subject']);
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testInlineEditingPatch($id)
    {
        $patchedTask = ['subject' => 'Patched subject'];
        $this->client->request(
            'PATCH',
            $this->getUrl('oro_api_patch_entity_data', [
                'id' => $id,
                'className' => 'Oro_Bundle_TaskBundle_Entity_Task'
            ]),
            [],
            [],
            [],
            json_encode($patchedTask)
        );
        self::assertJsonResponseStatusCodeEquals($this->client->getResponse(), 200);

        $this->client->request('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $task = $this->getJsonResponseContent($this->client->getResponse(), 200);
        self::assertEquals('Patched subject', $task['subject']);
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testDelete($id)
    {
        $this->client->request('DELETE', $this->getUrl('oro_api_delete_task', ['id' => $id]));
        $result = $this->client->getResponse();
        self::assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $result = $this->client->getResponse();
        self::assertJsonResponseStatusCodeEquals($result, 404);
    }
}
