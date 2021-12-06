<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Tests\Functional\Api\DataFixtures\LoadUserData;
use Oro\Bundle\UserProBundle\Tests\Functional\DataFixtures\LoadOrganizationData;

class TaskControllerACLTest extends WebTestCase
{
    protected const USER_NAME = 'user_wo_permissions';
    protected const USER_PASSWORD = 'user_api_key';

    protected function setUp(): void
    {
        $this->initClient(
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );

        $fixtures = [
            '@OroTaskBundle/Tests/Functional/Api/DataFixtures/task_data.yml',
            LoadUserData::class
        ];
        if (class_exists(LoadOrganizationData::class)) {
            $fixtures[] = LoadOrganizationData::class;
        }
        $this->loadFixtures($fixtures);
    }

    private function getTask(): Task
    {
        return $this->getReference('task1');
    }

    public function testCreate()
    {
        $request = [
            'subject' => 'New task',
            'description' => 'New description',
            'dueDate' => '2014-03-04T20:00:00+0000',
            'taskPriority' => 'high',
            'owner' => '1',
        ];

        $this->client->jsonRequest(
            'POST',
            $this->getUrl('oro_api_post_task'),
            $request,
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        self::assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testCget()
    {
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_tasks'),
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        self::assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testGet()
    {
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_task', ['id' => $this->getTask()->getId()]),
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        self::assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testPut()
    {
        $updatedTask = ['subject' => 'Updated subject'];
        $this->client->jsonRequest(
            'PUT',
            $this->getUrl('oro_api_put_task', ['id' => $this->getTask()->getId()]),
            $updatedTask,
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        self::assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testDelete()
    {
        $this->client->jsonRequest(
            'DELETE',
            $this->getUrl('oro_api_delete_task', ['id' => $this->getTask()->getId()]),
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        self::assertJsonResponseStatusCodeEquals($result, 403);
    }
}
