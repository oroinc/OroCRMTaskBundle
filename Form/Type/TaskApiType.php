<?php

namespace Oro\Bundle\TaskBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\SoapBundle\Form\EventListener\PatchSubscriber;
use Oro\Bundle\TaskBundle\Entity\Task;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for old REST API to add createdAt field
 */
class TaskApiType extends TaskType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'createdAt',
            OroDateTimeType::class,
            [
                'required' => false,
            ]
        );

        $builder->addEventSubscriber(new PatchSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Task::class,
                'csrf_protection' => false
            ]
        );
    }

    /**
     * {@inheritdoc}
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
        return 'task';
    }

    protected function addDueDateField(FormBuilderInterface $builder)
    {
        // no any additional constraints for "dueDate" in API
        $builder
            ->add(
                'dueDate',
                OroDateTimeType::class,
                ['required' => false]
            );
    }

    protected function updateDueDateFieldConstraints(FormEvent $event)
    {
        // no any additional constraints for "dueDate" in API
    }
}
