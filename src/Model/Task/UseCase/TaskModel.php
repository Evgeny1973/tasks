<?php

declare(strict_types=1);

namespace App\Model\Task\UseCase;

final readonly class TaskModel
{
    public function __construct(
        private(set) int $id,
        private(set) string $status,
    ) {
    }
}
