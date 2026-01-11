<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Model\Task\Entity\TaskRepository;
use App\Response\ApiResponseInterface;
use App\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class ListController
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {
    }

    #[Route('/tasks', name: 'tasks_list', methods: ['GET'])]
    public function __invoke(): ApiResponseInterface
    {
        $tasks = $this->taskRepository->findAll();

        return new SuccessResponse(
            data: $tasks,
            message: null,
            resultCode: Response::HTTP_OK,
        );
    }
}
