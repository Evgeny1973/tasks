<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Model\Task\UseCase\Create\CreateTaskCommand;
use App\Model\Task\UseCase\Get\GetTaskInfoQuery;
use App\Model\Task\UseCase\Get\TaskInfoModel;
use App\Service\CommandQueryHandleTrait;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

readonly class CreateController
{
    use CommandQueryHandleTrait;

    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
    ) {
    }

    #[Route('/tasks', name: 'tasks_create', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateTaskCommand $command): TaskInfoModel
    {
        $taskModel = $this->handleCommand($command);

        return $this->handleQuery(GetTaskInfoQuery::fromModel($taskModel));
    }
}
