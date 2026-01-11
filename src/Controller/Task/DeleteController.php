<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Model\Task\Entity\Task;
use App\Model\Task\Entity\TaskRepository;
use App\Response\ApiResponseInterface;
use App\Response\ErrorResponse;
use App\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class DeleteController
{
    public function __construct(private TaskRepository $taskRepository)
    {
    }

    #[Route('/tasks/{id}', name: 'tasks_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function __invoke(?Task $task): ApiResponseInterface
    {
        if (is_null($task)) {
            return new ErrorResponse(
                message: 'Task not found',
                resultCode: Response::HTTP_NOT_FOUND
            );
        }

        $this->taskRepository->delete($task);

        return new SuccessResponse(
            data: null,
            message: 'Task deleted successfully',
            resultCode: Response::HTTP_OK,
        );
    }
}
