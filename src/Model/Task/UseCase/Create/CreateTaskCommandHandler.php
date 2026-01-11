<?php

declare(strict_types=1);

namespace App\Model\Task\UseCase\Create;

use App\Model\Task\Entity\Task;
use App\Model\Task\Entity\TaskRepository;
use App\Model\Task\UseCase\TaskModel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateTaskCommandHandler
{
    public function __construct(private TaskRepository $repository)
    {
    }

    public function __invoke(CreateTaskCommand $command): TaskModel
    {
        $task = Task::create(
            title: $command->title,
            description: $command->description,
            status: $command->status,
        );

        $this->repository->store($task);

        return new TaskModel($task->id, $task->status);
    }
}
