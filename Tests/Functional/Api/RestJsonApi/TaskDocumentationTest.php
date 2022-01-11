<?php

namespace Oro\Bundle\TaskBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Request\ApiAction;
use Oro\Bundle\ApiBundle\Tests\Functional\DocumentationTestTrait;
use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;

/**
 * @group regression
 */
class TaskDocumentationTest extends RestJsonApiTestCase
{
    use DocumentationTestTrait;

    /** @var string used in DocumentationTestTrait */
    private const VIEW = 'rest_json_api';

    private static bool $isDocumentationCacheWarmedUp = false;

    protected function setUp(): void
    {
        parent::setUp();
        if (!self::$isDocumentationCacheWarmedUp) {
            $this->warmUpDocumentationCache();
            self::$isDocumentationCacheWarmedUp = true;
        }
    }

    public function testTaskActivityTargets(): void
    {
        $docs = $this->getEntityDocsForAction('tasks', ApiAction::GET);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>Records associated with the task record.</p>',
            $resourceData['response']['activityTargets']['description']
        );
    }

    public function testTargetEntityActivityTasks(): void
    {
        $docs = $this->getEntityDocsForAction('contacts', ApiAction::GET);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The tasks associated with the contact record.</p>',
            $resourceData['response']['activityTasks']['description']
        );
    }

    public function testTaskActivityTargetsGetSubresource(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('tasks', 'activityTargets', ApiAction::GET_SUBRESOURCE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get activity targets', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve records associated with a specific task record.</p>',
            $resourceData['documentation']
        );
    }

    public function testTaskActivityTargetsGetRelationship(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('tasks', 'activityTargets', ApiAction::GET_RELATIONSHIP);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get "activity targets" relationship', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the IDs of records associated with a specific task record.</p>',
            $resourceData['documentation']
        );
    }

    public function testTargetEntityActivityTasksGetSubresource(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('contacts', 'activityTasks', ApiAction::GET_SUBRESOURCE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get activity tasks', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the records of the tasks associated with a specific contact record.</p>',
            $resourceData['documentation']
        );
    }

    public function testTargetEntityActivityTasksGetRelationship(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('contacts', 'activityTasks', ApiAction::GET_RELATIONSHIP);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get "activity tasks" relationship', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the IDs of the tasks associated with a specific contact record.</p>',
            $resourceData['documentation']
        );
    }
}
