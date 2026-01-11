<?php

declare(strict_types=1);

namespace App\Response;

interface ApiResponseInterface
{
    public function getResultCode(): int;
}
