<?php

namespace Oro\Bundle\TaskBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Extend\Entity\Autocomplete\OroTaskBundle_Entity_Task;
use Oro\Bundle\ActivityBundle\Model\ActivityInterface;
use Oro\Bundle\ActivityBundle\Model\ExtendActivity;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\ReminderBundle\Entity\RemindableInterface;
use Oro\Bundle\ReminderBundle\Entity\Reminder;
use Oro\Bundle\ReminderBundle\Model\ReminderData;
use Oro\Bundle\TaskBundle\Entity\Repository\TaskRepository;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Task entity class
 *
 * @method EnumOptionInterface getStatus()
 * @method Task setStatus(EnumOptionInterface $status)
 * @mixin OroTaskBundle_Entity_Task
 */
#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'orocrm_task')]
#[ORM\Index(columns: ['due_date', 'id'], name: 'task_due_date_idx')]
#[ORM\Index(columns: ['updated_at', 'id'], name: 'task_updated_at_idx')]
#[Config(
    routeName: 'oro_task_index',
    routeView: 'oro_task_view',
    defaultValues: [
        'entity' => ['icon' => 'fa-tasks'],
        'ownership' => [
            'owner_type' => 'USER',
            'owner_field_name' => 'owner',
            'owner_column_name' => 'owner_id',
            'organization_field_name' => 'organization',
            'organization_column_name' => 'organization_id'
        ],
        'security' => ['type' => 'ACL', 'category' => 'account_management'],
        'dataaudit' => ['auditable' => true],
        'workflow' => ['show_step_in_grid' => false],
        'reminder' => [
            'reminder_template_name' => 'task_reminder',
            'reminder_flash_template_identifier' => 'task_template'
        ],
        'grouping' => ['groups' => ['activity']],
        'activity' => [
            'route' => 'oro_task_activity_view',
            'acl' => 'oro_task_view',
            'action_button_widget' => 'oro_add_task_button',
            'action_link_widget' => 'oro_add_task_link'
        ],
        'tag' => ['enabled' => true],
        'grid' => ['default' => 'tasks-grid', 'context' => 'task-for-context-grid']
    ]
)]
class Task implements
    RemindableInterface,
    DatesAwareInterface,
    ActivityInterface,
    ExtendEntityInterface
{
    use DatesAwareTrait;
    use ExtendActivity;
    use ExtendEntityTrait;

    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'subject', type: Types::STRING, length: 255, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $subject = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $description = null;

    #[ORM\Column(name: 'due_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?\DateTimeInterface $dueDate = null;

    #[ORM\ManyToOne(targetEntity: TaskPriority::class)]
    #[ORM\JoinColumn(name: 'task_priority_name', referencedColumnName: 'name', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?TaskPriority $taskPriority = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?User $owner = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected ?User $createdBy = null;

    /**
     * @var Collection|Reminder[]
     */
    protected ?Collection $reminders = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected ?OrganizationInterface $organization = null;

    public function __construct()
    {
        $this->reminders = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @return bool
     */
    public function isDueDateExpired()
    {
        return $this->getDueDate() &&  $this->getDueDate() < new \DateTime();
    }

    public function setDueDate(?\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return TaskPriority
     */
    public function getTaskPriority()
    {
        return $this->taskPriority;
    }

    public function setTaskPriority(TaskPriority $taskPriority)
    {
        $this->taskPriority = $taskPriority;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return mixed|null
     */
    public function getOwnerId()
    {
        return $this->getOwner() ? $this->getOwner()->getId() : null;
    }

    /**
     * @param User $owner
     */
    public function setOwner($owner = null)
    {
        $this->owner = $owner;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     * @return $this
     */
    public function setCreatedBy(?User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    #[\Override]
    public function getReminders()
    {
        return $this->reminders;
    }

    /**
     * @param Reminder $reminder
     * @return $this
     */
    public function addReminder(Reminder $reminder)
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
        }

        return $this;
    }

    /**
     * @param Reminder $reminder
     * @return $this
     */
    public function removeReminder(Reminder $reminder)
    {
        $this->reminders->removeElement($reminder);

        return $this;
    }

    #[\Override]
    public function setReminders(Collection $reminders)
    {
        $this->reminders = $reminders;
    }

    #[\Override]
    public function getReminderData()
    {
        $result = new ReminderData();

        $result->setSubject($this->getSubject());
        $result->setExpireAt($this->getDueDate());
        $result->setRecipient($this->getOwner());

        return $result;
    }

    /**
     * @param Organization|null $organization
     * @return $this
     */
    public function setOrganization(?Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return string
     */
    #[\Override]
    public function __toString()
    {
        return (string)$this->getSubject();
    }
}
