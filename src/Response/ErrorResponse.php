<?php

declare(strict_types=1);

namespace App\Response;

final class ErrorResponse extends AbstractResponse implements ApiResponseInterface
{
    public function __construct(?string $message, int $resultCode)
    {
        parent::__construct(
            success: false,
            resultCode: $resultCode,
            message: $message,
            data: null
        );
    }

    public function getResultCode(): int
    {
        return $this->resultCode;
    }
}
