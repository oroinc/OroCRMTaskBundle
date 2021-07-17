<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Oro\Bundle\EntityExtendBundle\Tests\Unit\Fixtures\TestEnumValue;
use Oro\Bundle\FormBundle\Form\Type\OroResizeableRichTextType;
use Oro\Bundle\ReminderBundle\Entity\Reminder;
use Oro\Bundle\ReminderBundle\Form\Type\MethodType;
use Oro\Bundle\ReminderBundle\Form\Type\ReminderCollectionType;
use Oro\Bundle\ReminderBundle\Model\ReminderInterval;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TaskBundle\Form\Type\TaskType;
use Oro\Bundle\TaskBundle\Tests\Unit\Stub\TaskStub;
use Oro\Bundle\TranslationBundle\Form\Type\TranslatableEntityType;
use Oro\Component\Testing\Unit\EntityTrait;
use Oro\Component\Testing\Unit\Form\Type\Stub\EntityType;
use Oro\Component\Testing\Unit\Form\Type\Stub\EnumSelectType as EnumSelectTypeStub;
use Oro\Component\Testing\Unit\FormIntegrationTestCase;
use Oro\Component\Testing\Unit\PreloadedExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskTypeTest extends FormIntegrationTestCase
{
    use EntityTrait;

    /**
     * @var TaskType
     */
    protected $formType;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->formType = new TaskType();
        parent::setUp();
    }

    /**
     * @return array
     */
    protected function getExtensions()
    {
        $statusEnumField = new EnumSelectTypeStub(
            [
                new TestEnumValue('open', 'Open'),
                new TestEnumValue('in_progress', 'In progress'),
                new TestEnumValue('closed', 'Closed'),
            ]
        );

        $lowPriority = new TaskPriority('low');
        $lowPriority->setLabel('Low');

        $normalPriority = new TaskPriority('normal');
        $normalPriority->setLabel('Normal');

        $highPriority = new TaskPriority('high');
        $highPriority->setLabel('High');

        $translatableEntityType = new EntityType([
            $lowPriority->getName() => $lowPriority,
            $normalPriority->getName() => $normalPriority,
            $highPriority->getName() => $highPriority,
        ], TranslatableEntityType::class);

        $reminderMethodType = new EntityType([
            'Email' => 'email',
            'Flash notification' => 'flash',
        ], MethodType::class);

        return [
            new PreloadedExtension(
                [
                    OroResizeableRichTextType::class => new TextareaType(),
                    EnumSelectType::class => $statusEnumField,
                    TranslatableEntityType::class => $translatableEntityType,
                    ReminderCollectionType::class => new ReminderCollectionType(),
                    MethodType::class => $reminderMethodType,
                ],
                []
            ),
            $this->getValidatorExtension(true),
        ];
    }

    /**
     * @dataProvider submitDataProvider
     */
    public function testSubmit(
        Task $defaultData,
        array $submittedData,
        Task $expectedData
    ) {
        $form = $this->factory->create(TaskType::class, $defaultData);

        self::assertEquals($defaultData, $form->getData());

        $form->submit($submittedData);

        self::assertTrue($form->isSubmitted());
        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid(), $form->getErrors(true, false));

        static::assertEquals($expectedData, $form->getData());
    }

    /**
     * @return array
     */
    public function submitDataProvider()
    {
        $lowTaskPriority = new TaskPriority('low');
        $lowTaskPriority->setLabel('Low');

        $defaultTask = new TaskStub();
        $defaultTask->setSubject('Old subject');
        $defaultTask->setDescription('Old description');
        $defaultTask->setStatus(new TestEnumValue('open', 'Open'));
        $defaultTask->setTaskPriority($lowTaskPriority);

        $normalTaskPriority = new TaskPriority('normal');
        $normalTaskPriority->setLabel('Normal');

        $emailReminder = new Reminder();
        $emailReminder->setMethod('email');
        $emailReminder->setInterval(new ReminderInterval(15, ReminderInterval::UNIT_MINUTE));

        $flashReminder = new Reminder();
        $flashReminder->setMethod('flash');
        $flashReminder->setInterval(new ReminderInterval(10, ReminderInterval::UNIT_MINUTE));

        $expectedTask = new TaskStub();
        $expectedTask->setSubject('New subject');
        $expectedTask->setDescription('New description');
        $expectedTask->setStatus(new TestEnumValue('in_progress', 'In progress'));
        $expectedTask->setDueDate(new \DateTime('2040-03-04T20:00:00+0000'));
        $expectedTask->setTaskPriority($normalTaskPriority);
        $expectedTask->setReminders(new ArrayCollection([$emailReminder, $flashReminder]));

        return [
            'task update' => [
                'defaultData' => $defaultTask,
                'submittedData' => [
                    'subject' => 'New subject',
                    'description' => 'New description',
                    'dueDate' => '2040-03-04T20:00:00+0000',
                    'status' => 'in_progress',
                    'taskPriority' => 'normal',
                    'reminders' => [
                        [
                            'method'   => 'Email',
                            'interval' => [
                                'number' => 15,
                                'unit'   => ReminderInterval::UNIT_MINUTE
                            ]
                        ],
                        [
                            'method'   => 'Flash notification',
                            'interval' => [
                                'number' => 10,
                                'unit'   => ReminderInterval::UNIT_MINUTE
                            ]
                        ],
                    ]
                ],
                'expectedData' => $expectedTask
            ]
        ];
    }
}
