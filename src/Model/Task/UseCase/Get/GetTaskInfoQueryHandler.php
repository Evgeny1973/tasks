<?php

declare(strict_types=1);

namespace App\Model\Task\UseCase\Get;

use App\Model\Task\Entity\TaskRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetTaskInfoQueryHandler
{
    public function __construct(
        private TaskRepository $repository,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(GetTaskInfoQuery $query): TaskInfoModel
    {
        $task = $this->repository->findById($query->getTaskId());

        if (is_null($task)) {
            throw new BadRequestHttpException('Task not found');
        }

        return $this->objectMapper->map($task, TaskInfoModel::class);
    }
}
