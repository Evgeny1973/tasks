<?php

declare(strict_types=1);

namespace App\Model\Task\UseCase\Create;

use App\Model\Task\Enum\TaskStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateTaskCommand
{
    #[Assert\NotBlank(message: 'Title cannot be empty')]
    public string $title;

    #[Assert\NotBlank(message: 'Description cannot be empty')]
    #[Assert\Length(max: 255, maxMessage: 'Title is too long')]
    public string $description;

    #[Assert\Choice(callback: [TaskStatusEnum::class, 'getValues'], message: 'You choose a wrong status')]
    public string $status;
}
