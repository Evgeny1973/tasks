<?php

declare(strict_types=1);

namespace App\Model\Task\UseCase\Get;

final readonly class TaskInfoModel
{
    public function __construct(
        private(set) int $id,
        private(set) string $title,
        private(set) string $description,
        private(set) string $status,
        private(set) \DateTimeImmutable $createdAt,
        private(set) ?\DateTimeImmutable $updatedAt,
    ) {
    }
}
