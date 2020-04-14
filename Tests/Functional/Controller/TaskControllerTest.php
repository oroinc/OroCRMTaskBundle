<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Controller;

use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    protected const GRID_OF_TASK = 'activity-tasks-grid';
    protected const GRID_OF_USERS_TASK = 'user-tasks-grid';

    /**
     * @var Task $task
     */
    protected $task;

    protected function setUp(): void
    {
        $this->initClient([], static::generateBasicAuthHeader());
        $this->client->useHashNavigation(true);
        $this->loadFixtures(['@OroTaskBundle/Tests/Functional/DataFixtures/task_data.yml']);
        $this->task = $this->getReference('task1');
    }

    public function testTasksWidgetAction()
    {
        /** Return list of the task */
        $this->client->request(Request::METHOD_GET, $this->getUrl('oro_task_widget_sidebar_tasks'));
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);

        /** Assert by task name */
        static::assertStringContainsString('Meet James', $response->getContent());
        static::assertStringContainsString('Check email', $response->getContent());
        static::assertStringContainsString('Open new bank account', $response->getContent());
    }

    public function testInfoAction()
    {
        /** Return task by id */
        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('oro_task_widget_info', ['id' => $this->task->getId()])
        );
        $response = $this->client->getResponse();

        /** Assert by prepared field */
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        static::assertStringContainsString('Meet James', $response->getContent());
        static::assertStringContainsString('Meet James in the office', $response->getContent());
        static::assertStringContainsString('Normal', $response->getContent());
        static::assertStringContainsString('John Doe', $response->getContent());
    }

    public function testActivityAction()
    {
        /** @var EntityRoutingHelper $helper */
        $helper = self::getContainer()->get('oro_entity.routing_helper');
        $entityClass = $helper->getUrlSafeClassName(Task::class);
        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl(
                'oro_task_activity_view',
                ['entityId' => $this->task->getId(), 'entityClass' => $entityClass]
            )
        );
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        static::assertStringContainsString(self::GRID_OF_TASK, $response->getContent());
    }

    public function testUserTasksAction()
    {
        $userManager = self::getContainer()->get('oro_user.manager');
        $user = $userManager->findUserByEmail(LoadAdminUserData::DEFAULT_ADMIN_EMAIL);

        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('oro_task_user_tasks', ['user' => $user->getId()])
        );
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);
    }

    public function testMyTasksAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('oro_task_my_tasks')
        );
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        static::assertStringContainsString(self::GRID_OF_USERS_TASK, $response->getContent());

        $response = $this->client->requestGrid(self::GRID_OF_USERS_TASK);
        $gridRecords = self::getJsonResponseContent($response, Response::HTTP_OK);
        self::assertCount(3, $gridRecords);
    }
}
