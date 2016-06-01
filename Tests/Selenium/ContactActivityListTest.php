<?php

namespace OroCRM\Bundle\TaskBundle\Tests\Selenium;

use Oro\Bundle\TestFrameworkBundle\Test\Selenium2TestCase;
use OroCRM\Bundle\TaskBundle\Tests\Selenium\Pages\Task;

class ContactTaskActivityListTest extends Selenium2TestCase
{
    protected function setUp()
    {
        if (!class_exists('OroCRM\Bundle\ContactBundle\Tests\Selenium\Pages\Contacts')) {
            $this->markTestSkipped('Contact Bundle Is Not Found');
        }

        parent::setUp();
    }

    /**
     * @return string
     */
    public function testCreateContact()
    {
        $contactName = 'Contact_'.mt_rand();

        $login = $this->login();
        /** @var Contacts $login */
        $login->openContacts('OroCRM\Bundle\ContactBundle')
            ->assertTitle('All - Contacts - Customers')
            ->add()
            ->assertTitle('Create Contact - Contacts - Customers')
            ->setFirstName($contactName . '_first')
            ->setLastName($contactName . '_last')
            ->setOwner('admin')
            ->setEmail($contactName . '@mail.com')
            ->save();

        return $contactName;
    }

    /**
     * @depends testCreateContact
     * @param $contactName
     */
    public function testAddTaskActivity($contactName)
    {
        $subject = 'Tasks_' . mt_rand();

        $login = $this->login();

        $task = $login->openContacts('OroCRM\Bundle\ContactBundle')
            ->filterBy('Email', $contactName . '@mail.com')
            ->open([$contactName])
            ->runActionInGroup('Add task')
            ->openTask('OroCRM\Bundle\TaskBundle');

        /** @var Task $task */
        $task
            ->setSubject($subject)
            ->setDescription($subject)
            ->createTask()
            ->assertMessage('Task created successfully')
            ->verifyActivity('Task', $subject);
    }

    public function testCloseWidgetWindow()
    {
        $login = $this->login();
        $login->closeWidgetWindow();
    }
}
