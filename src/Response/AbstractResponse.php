<?php

declare(strict_types=1);

namespace App\Response;

class AbstractResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly int $resultCode,
        public readonly ?string $message,
        public readonly mixed $data,
    ) {
    }
}
