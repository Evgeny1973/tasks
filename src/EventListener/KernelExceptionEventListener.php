<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Response\ErrorResponse;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: 'kernel.exception')]
final readonly class KernelExceptionEventListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     *
     * @throws ExceptionInterface
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $errorResponse = $this->resolveResponse($exception);

        $jsonResponse = new JsonResponse(
            data: $this->serializer->serialize($errorResponse, JsonEncoder::FORMAT),
            status: $errorResponse->resultCode,
            json: true
        );

        $event->setResponse($jsonResponse);
    }

    private function resolveResponse(\Throwable $exception): ErrorResponse
    {
        $message = $exception->getMessage();

        if (404 === $this->resolveExceptionCode($exception)) {
            $message = 'Not Found';
        }

        return new ErrorResponse(
            message: $message,
            resultCode: $this->resolveExceptionCode($exception)
        );
    }

    private function resolveExceptionCode(\Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        $statusCode = $exception->getCode();

        if ($statusCode < Response::HTTP_CONTINUE || $statusCode > Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $statusCode;
    }
}
