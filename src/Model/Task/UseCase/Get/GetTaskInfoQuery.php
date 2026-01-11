<?php

declare(strict_types=1);

namespace App\Model\Task\UseCase\Get;

use App\Model\Task\UseCase\TaskModel;

final readonly class GetTaskInfoQuery
{
    public function __construct(private int $taskId)
    {
    }

    public static function fromModel(TaskModel $taskModel): self
    {
        return new self($taskModel->id);
    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }
}
