<?php

namespace Oro\Bundle\TaskBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;

/**
* Entity that represents Task Priority
*
*/
#[ORM\Entity]
#[ORM\Table(name: 'orocrm_task_priority')]
#[Config(
    defaultValues: [
        'grouping' => ['groups' => ['dictionary']],
        'dictionary' => ['virtual_fields' => ['label'], 'search_fields' => ['label'], 'representation_field' => 'label']
    ]
)]
class TaskPriority
{
    #[ORM\Column(name: 'name', type: Types::STRING, length: 32)]
    #[ORM\Id]
    protected ?string $name = null;

    #[ORM\Column(name: 'label', type: Types::STRING, length: 255, unique: true)]
    protected ?string $label = null;

    /**
     * @var string
     */
    #[ORM\Column(name: '`order`', type: Types::INTEGER)]
    protected $order;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get Task priority name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Task priority label
     *
     * @param string $label
     * @return TaskPriority
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get task priority label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order
     *
     * @param string $order
     * @return TaskPriority
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    #[\Override]
    public function __toString()
    {
        return (string) $this->label;
    }
}
