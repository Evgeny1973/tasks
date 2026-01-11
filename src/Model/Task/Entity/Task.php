<?php

namespace App\Model\Task\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
#[ORM\HasLifecycleCallbacks]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private(set) ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private(set) ?\DateTimeImmutable $updatedAt = null;

    public static function create(string $title, string $description, string $status): self
    {
        $task = new self();
        $task->title = $title;
        $task->description = $description;
        $task->status = $status;

        return $task;
    }

    public function update(string $title, string $description, string $status): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));
    }
}

