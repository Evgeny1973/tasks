<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

trait CommandQueryHandleTrait
{
    private readonly MessageBusInterface $commandBus;
    private readonly MessageBusInterface $queryBus;

    private function handleCommand(mixed $message): mixed
    {
        $envelope = $this->commandBus->dispatch($message);

        return $envelope->last(HandledStamp::class)?->getResult();
    }

    private function handleQuery(mixed $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);

        return $envelope->last(HandledStamp::class)?->getResult();
    }
}
