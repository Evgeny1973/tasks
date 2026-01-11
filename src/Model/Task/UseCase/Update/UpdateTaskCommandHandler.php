<?php

declare(strict_types=1);

namespace App\Model\Task\UseCase\Update;

use App\Model\Task\Entity\TaskRepository;
use App\Model\Task\UseCase\TaskModel;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateTaskCommandHandler
{
    public function __construct(private TaskRepository $repository)
    {
    }

    public function __invoke(UpdateTaskCommand $command): TaskModel
    {
        $task = $this->repository->findById($command->id);

        if (is_null($task)) {
            throw new BadRequestHttpException('Task not found');
        }

        $task->update(
            title: $command->title,
            description: $command->description,
            status: $command->status,
        );

        $this->repository->flush();

        return new TaskModel($task->id, $task->status);
    }
}
