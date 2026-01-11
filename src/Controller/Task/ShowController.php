<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Model\Task\Entity\Task;
use App\Response\ApiResponseInterface;
use App\Response\ErrorResponse;
use App\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class ShowController
{
    #[Route('/tasks/{id}', name: 'tasks_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function __invoke(?Task $task): ApiResponseInterface
    {
        if (is_null($task)) {
            return new ErrorResponse(
                message: 'Task not found',
                resultCode: Response::HTTP_NOT_FOUND
            );
        }

        return new SuccessResponse(
            data: $task,
            message: null,
            resultCode: Response::HTTP_OK,
        );
    }
}
