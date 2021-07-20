<?php

namespace Oro\Bundle\TaskBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\FormBundle\Form\Type\OroResizeableRichTextType;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Oro\Bundle\ReminderBundle\Form\Type\ReminderCollectionType;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Entity\TaskPriority;
use Oro\Bundle\TranslationBundle\Form\Type\TranslatableEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form type for Task entity
 */
class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'subject',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'oro.task.subject.label'
                ]
            )
            ->add(
                'description',
                OroResizeableRichTextType::class,
                [
                    'required' => false,
                    'label' => 'oro.task.description.label'
                ]
            );
        $this->addDueDateField($builder);
        $builder
            ->add(
                'status',
                EnumSelectType::class,
                [
                    'label' => 'oro.task.status.label',
                    'enum_code' => 'task_status',
                    'required' => true,
                    'constraints' => [new Assert\NotNull()]
                ]
            )
            ->add(
                'taskPriority',
                TranslatableEntityType::class,
                [
                    'label' => 'oro.task.task_priority.label',
                    'class' => TaskPriority::class,
                    'required' => true,
                    'choice_label' => 'label',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('priority')->orderBy('priority.order');
                    }
                ]
            )
            ->add(
                'reminders',
                ReminderCollectionType::class,
                [
                    'required' => false,
                    'label' => 'oro.reminder.entity_plural_label'
                ]
            );
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'postSetData']);
    }

    /**
     * Post set data handler
     */
    public function postSetData(FormEvent $event)
    {
        $this->updateDueDateFieldConstraints($event);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Task::class
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oro_task';
    }

    protected function addDueDateField(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                'dueDate',
                OroDateTimeType::class,
                [
                    'required' => false,
                    'label' => 'oro.task.due_date.label',
                    'constraints' => [
                        $this->getDueDateValidationConstraint(new \DateTime('now', new \DateTimeZone('UTC')))
                    ]
                ]
            );
    }

    protected function updateDueDateFieldConstraints(FormEvent $event)
    {
        /** @var Task $data */
        $data = $event->getData();
        if ($data && $data->getCreatedAt()) {
            FormUtils::replaceField(
                $event->getForm(),
                'dueDate',
                [
                    'constraints' => [
                        $this->getDueDateValidationConstraint($data->getCreatedAt())
                    ]
                ]
            );
        }
    }

    /**
     * @param \DateTime $startDate
     *
     * @return Assert\GreaterThanOrEqual
     */
    protected function getDueDateValidationConstraint(\DateTime $startDate)
    {
        return new Assert\GreaterThanOrEqual(
            [
                'value'   => $startDate,
                'message' => 'oro.task.due_date_not_in_the_past'
            ]
        );
    }
}
