<?php

namespace Oro\Bundle\TaskBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Oro\Bundle\TaskBundle\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

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
                'text',
                [
                    'required' => true,
                    'label' => 'oro.task.subject.label'
                ]
            )
            ->add(
                'description',
                'oro_resizeable_rich_text',
                [
                    'required' => false,
                    'label' => 'oro.task.description.label'
                ]
            );
        $this->addDueDateField($builder);
        $builder
            ->add(
                'status',
                'oro_enum_select',
                [
                    'label' => 'oro.task.status.label',
                    'enum_code' => 'task_status',
                    'required' => true,
                    'constraints' => [new Assert\NotNull()]
                ]
            )
            ->add(
                'taskPriority',
                'translatable_entity',
                [
                    'label' => 'oro.task.task_priority.label',
                    'class' => 'Oro\Bundle\TaskBundle\Entity\TaskPriority',
                    'required' => true,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('priority')->orderBy('priority.order');
                    }
                ]
            )
            ->add(
                'reminders',
                'oro_reminder_collection',
                [
                    'required' => false,
                    'label' => 'oro.reminder.entity_plural_label'
                ]
            );
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'postSetData']);
    }

    /**
     * Post set data handler
     *
     * @param FormEvent $event
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
                'data_class' => 'Oro\Bundle\TaskBundle\Entity\Task',
                'intention' => 'task',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oro_task';
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addDueDateField(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                'dueDate',
                'oro_datetime',
                [
                    'required' => false,
                    'label' => 'oro.task.due_date.label',
                    'constraints' => [
                        $this->getDueDateValidationConstraint(new \DateTime('now', new \DateTimeZone('UTC')))
                    ]
                ]
            );
    }

    /**
     * @param FormEvent $event
     */
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
                'message' => 'Due date must not be in the past'
            ]
        );
    }
}
