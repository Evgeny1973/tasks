<?php

declare(strict_types=1);

namespace App\Model\Task\Enum;

enum TaskStatusEnum: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DELETED = 'deleted';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
