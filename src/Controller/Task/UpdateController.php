<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Model\Task\UseCase\Get\GetTaskInfoQuery;
use App\Model\Task\UseCase\Get\TaskInfoModel;
use App\Model\Task\UseCase\Update\UpdateTaskCommand;
use App\Service\CommandQueryHandleTrait;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

readonly class UpdateController
{
    use CommandQueryHandleTrait;

    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
    ) {
    }

    #[Route('/tasks/{id}', name: 'tasks_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function __invoke(int $id, #[MapRequestPayload] UpdateTaskCommand $command): TaskInfoModel
    {
        $command->id = $id;

        $taskModel = $this->handleCommand($command);

        return $this->handleQuery(GetTaskInfoQuery::fromModel($taskModel));
    }
}
