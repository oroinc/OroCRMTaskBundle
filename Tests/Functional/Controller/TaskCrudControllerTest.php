<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Controller;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskCrudControllerTest extends WebTestCase
{
    private const GRID_OF_TASKS = 'tasks-grid';

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

    private function getDueDate(): string
    {
        // Due Date must not be in the past
        $timeZone = self::getContainer()->get('oro_locale.settings')->getTimeZone();
        $date = new \DateTime('2036-01-01 00:00:00', new \DateTimeZone($timeZone));

        return $date->format(\DateTime::RFC3339);
    }

    public function testIndex()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->getUrl('oro_task_index'));
        $response = $this->client->getResponse();
        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertStringContainsString(self::GRID_OF_TASKS, $crawler->html());
        self::assertStringContainsString('Create Task', $response->getContent());

        $response = $this->client->requestGrid(self::GRID_OF_TASKS);
        $gridRecords = self::getJsonResponseContent($response, Response::HTTP_OK);
        self::assertCount(3, $gridRecords);
    }

    public function testCreate()
    {
        $userManager = self::getContainer()->get('oro_user.manager');
        /** @var User $user */
        $user = $userManager->findUserByEmail(LoadAdminUserData::DEFAULT_ADMIN_EMAIL);

        $crawler = $this->client->request(Request::METHOD_GET, $this->getUrl('oro_task_create'));

        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_task[owner]'] = $user->getId();
        $form['oro_task[subject]'] = 'New task';
        $form['oro_task[description]'] = 'New description';
        $form['oro_task[dueDate]'] = $this->getDueDate();

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        $task = $this->getTaskBy(['subject' => 'New task']);

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertStringContainsString('Task saved', $crawler->html());
        self::assertNotNull($task);
        $this->removeTask($task);
    }

    public function testUpdate()
    {
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('oro_task_update', ['id' => $this->getTask()->getId()])
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_task[subject]'] = 'Subject updated';
        $form['oro_task[description]'] = 'Description updated';
        $form['oro_task[dueDate]'] = $this->getDueDate();

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        $task = $this->getTaskBy(['subject' => 'Subject updated']);

        self::assertNotNull($task);
        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertStringContainsString('Task saved', $crawler->html());
    }

    /**
     * @depends testUpdate
     */
    public function testView()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('oro_task_view', ['id' => $this->getTask()->getId()])
        );

        $response = $this->client->getResponse();
        $formattedDate = self::getContainer()
            ->get('oro_locale.formatter.date_time')
            ->format($this->getDueDate());

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertStringContainsString('General Information', $response->getContent());
        self::assertStringContainsString('Activity', $response->getContent());
        self::assertStringContainsString('Comments', $response->getContent());
        self::assertStringContainsString('Subject updated', $response->getContent());
        self::assertStringContainsString('Description updated', $response->getContent());
        self::assertStringContainsString('John Doe', $response->getContent());
        self::assertStringContainsString($formattedDate, $response->getContent());
    }

    private function removeTask(Task $task)
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->remove($task);
        $entityManager->flush();
        $entityManager->clear();
    }

    private function getTaskBy(array $criteria): ?Task
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        return $entityManager->getRepository(Task::class)->findOneBy($criteria);
    }
}
