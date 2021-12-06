<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\TaskBundle\Controller\Api\Rest\TaskController;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;

class TaskControllerTest extends WebTestCase
{
    private array $task = [
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
            $this->task['owner'] = $this->getContainer()->get('doctrine')->getRepository(User::class)
                ->findOneBy(['username' => self::USER_NAME])->getId();
        }
    }

    public function testCreate(): int
    {
        $this->client->jsonRequest('POST', $this->getUrl('oro_api_post_task'), $this->task);
        $task = $this->getJsonResponseContent($this->client->getResponse(), 201);

        return $task['id'];
    }

    /**
     * @depends testCreate
     */
    public function testCget()
    {
        $this->client->jsonRequest('GET', $this->getUrl('oro_api_get_tasks'));
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

        $date = '2014-03-04T20:00:00+0000';
        $ownerId = $this->task['owner'];
        $randomId = random_int($ownerId + 1, $ownerId + 100);

        $this->client->jsonRequest('GET', $baseUrl . '?createdAt>' . $date);
        self::assertCount(1, $this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->jsonRequest('GET', $baseUrl . '?createdAt<' . $date);
        self::assertEmpty($this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->jsonRequest('GET', $baseUrl . '?ownerId=' . $ownerId);
        self::assertCount(1, $this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->jsonRequest('GET', $baseUrl . '?ownerId=' . $randomId);
        self::assertEmpty($this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->jsonRequest('GET', $baseUrl . '?ownerUsername=' . self::USER_NAME);
        self::assertCount(1, $this->getJsonResponseContent($this->client->getResponse(), 200));

        $this->client->jsonRequest('GET', $baseUrl . '?ownerUsername<>' . self::USER_NAME);
        self::assertEmpty($this->getJsonResponseContent($this->client->getResponse(), 200));
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $id)
    {
        $this->client->jsonRequest('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $task = $this->getJsonResponseContent($this->client->getResponse(), 200);

        self::assertEquals($this->task['subject'], $task['subject']);
        self::assertNotEmpty($task[TaskController::FIELD_WORKFLOW_ITEM]);
        self::assertNotEmpty($task[TaskController::FIELD_WORKFLOW_STEP]);
    }

    /**
     * @depends testCreate
     */
    public function testPut(int $id)
    {
        $updatedTaskSubject = 'Updated subject';
        $this->client->jsonRequest(
            'PUT',
            $this->getUrl('oro_api_put_task', ['id' => $id]),
            array_merge($this->task, ['subject' => $updatedTaskSubject])
        );
        $result = $this->client->getResponse();
        self::assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->jsonRequest('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $task = $this->getJsonResponseContent($this->client->getResponse(), 200);

        self::assertEquals('Updated subject', $task['subject']);
        self::assertEquals($updatedTaskSubject, $task['subject']);
    }

    /**
     * @depends testCreate
     */
    public function testInlineEditingPatch(int $id)
    {
        $patchedTask = ['subject' => 'Patched subject'];
        $this->client->jsonRequest(
            'PATCH',
            $this->getUrl('oro_api_patch_entity_data', [
                'id' => $id,
                'className' => 'Oro_Bundle_TaskBundle_Entity_Task'
            ]),
            $patchedTask
        );
        $task = $this->getJsonResponseContent($this->client->getResponse(), 200);
        self::assertEquals('Patched subject', $task['fields']['subject']);
        self::assertArrayHasKey('updatedAt', $task['fields']);
        self::assertIsString($task['fields']['updatedAt']);

        $this->client->jsonRequest('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $task = $this->getJsonResponseContent($this->client->getResponse(), 200);
        self::assertEquals('Patched subject', $task['subject']);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(int $id)
    {
        $this->client->jsonRequest('DELETE', $this->getUrl('oro_api_delete_task', ['id' => $id]));
        $result = $this->client->getResponse();
        self::assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->jsonRequest('GET', $this->getUrl('oro_api_get_task', ['id' => $id]));
        $result = $this->client->getResponse();
        self::assertJsonResponseStatusCodeEquals($result, 404);
    }
}
