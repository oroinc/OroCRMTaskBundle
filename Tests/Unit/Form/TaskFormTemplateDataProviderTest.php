<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Form;

use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Form\TaskFormTemplateDataProvider;
use Oro\Component\Testing\ReflectionUtil;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class TaskFormTemplateDataProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var RouterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $router;

    /** @var TaskFormTemplateDataProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);

        $this->provider = new TaskFormTemplateDataProvider($this->router);
    }

    public function testDataWithEntityId()
    {
        $entity = new Task();
        ReflectionUtil::setId($entity, 1);

        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with('oro_task_update', ['id' => 1])
            ->willReturn('/update/1');

        $formView = $this->createMock(FormView::class);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->any())
            ->method('createView')
            ->willReturn($formView);

        $result = $this->provider->getData($entity, $form, $request);

        self::assertArrayHasKey('entity', $result);
        self::assertEquals($entity, $result['entity']);
        self::assertArrayHasKey('form', $result);
        self::assertEquals($formView, $result['form']);
        self::assertArrayHasKey('formAction', $result);
        self::assertEquals('/update/1', $result['formAction']);
    }

    public function testDataWithoutEntityId()
    {
        $entity = new Task();
        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with('oro_task_create')
            ->willReturn('/create');

        $formView = $this->createMock(FormView::class);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->any())
            ->method('createView')
            ->willReturn($formView);

        $result = $this->provider->getData($entity, $form, $request);

        self::assertArrayHasKey('entity', $result);
        self::assertEquals($entity, $result['entity']);
        self::assertArrayHasKey('form', $result);
        self::assertEquals($formView, $result['form']);
        self::assertArrayHasKey('formAction', $result);
        self::assertEquals('/create', $result['formAction']);
    }
}
