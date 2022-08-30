<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Controller;

use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private const GRID_OF_TASK = 'activity-tasks-grid';
    private const GRID_OF_USERS_TASK = 'user-tasks-grid';

    protected function setUp(): void
    {
        $this->initClient([], self::generateBasicAuthHeader());
        $this->client->useHashNavigation(true);
        $this->loadFixtures(['@OroTaskBundle/Tests/Functional/DataFixtures/task_data.yml']);
    }

    private function getTask(): Task
    {
        return $this->getReference('task1');
    }

    public function testTasksWidgetAction()
    {
        /** Return list of the task */
        $this->client->request(Request::METHOD_GET, $this->getUrl('oro_task_widget_sidebar_tasks'));
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);

        /** Assert by task name */
        self::assertStringContainsString('Meet James', $response->getContent());
        self::assertStringContainsString('Check email', $response->getContent());
        self::assertStringContainsString('Open new bank account', $response->getContent());
    }

    public function testInfoAction()
    {
        /** Return task by id */
        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('oro_task_widget_info', ['id' => $this->getTask()->getId()])
        );
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);

        /** Assert by prepared field */
        $content = $response->getContent();
        self::assertStringContainsString('Meet James', $content);
        self::assertStringContainsString('Meet James in the office', $content);
        self::assertStringContainsString('Normal', $content);
        self::assertStringContainsString('John Doe', $content);

        /** Assert widget actions are not exist, because not defined in template*/
        self::assertStringNotContainsString('Delete', $content);
        self::assertStringNotContainsString('View Task', $content);
        self::assertStringNotContainsString('Edit Task', $content);
    }

    public function testInfoActionForCalendar()
    {
        /** Return task by id */
        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('oro_task_widget_info', [
                'id' => $this->getTask()->getId(),
                '_widgetContainer'=>'Calendar',
                '_wid'=> 'f536db4e-9fe3-4720-85f9-e5ffc4ceb006'
            ])
        );
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);

        /** Assert widget actions are exist */
        $content = $response->getContent();
        self::assertStringContainsString('Delete', $content);
        self::assertStringContainsString('View Task', $content);
        self::assertStringContainsString('Edit Task', $content);
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
                ['entityId' => $this->getTask()->getId(), 'entityClass' => $entityClass]
            )
        );
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertStringContainsString(self::GRID_OF_TASK, $response->getContent());
    }

    public function testUserTasksAction()
    {
        $userManager = self::getContainer()->get('oro_user.manager');
        /** @var User $user */
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
        self::assertStringContainsString(self::GRID_OF_USERS_TASK, $response->getContent());

        $response = $this->client->requestGrid(self::GRID_OF_USERS_TASK);
        $gridRecords = self::getJsonResponseContent($response, Response::HTTP_OK);
        self::assertCount(3, $gridRecords);
    }
}
