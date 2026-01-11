<?php

declare(strict_types=1);

namespace App\Model\Task\Entity;

use Doctrine\ORM\EntityManagerInterface;

final readonly class TaskRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Task::class)->findAll();
    }

    public function findById(int $id): ?Task
    {
        return $this->em->getRepository(Task::class)->find($id);
    }

    public function store(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    public function flush(): void
    {
        $this->em->flush();
    }

    public function delete(Task $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }
}
