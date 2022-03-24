<?php

namespace App\EventSubscriber;

use App\Exception\AbstractException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;
use InvalidArgumentException;

/**
 * ExceptionSubscriber
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * __construct
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException'],
            ],
        ];
    }

    /**
     * onKernelException
     *
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $status    = $this->getExceptionStatusCode($throwable);
        $logLevel  = 'info';

        if (JsonResponse::HTTP_UNPROCESSABLE_ENTITY < $status) {
            $logLevel = 'error';
        }

        $context = $this->getContext($throwable);

        $this->logger->$logLevel($throwable->getMessage(), $context);

        $content = [
            'message' => $throwable->getMessage(),
            'code'    => $throwable->getCode(),
            'context' => $context,
        ];

        $event->setResponse(new JsonResponse($content, $status));
    }

    /**
     * getContext
     *
     * @param Throwable $throwable
     * @return array
     */
    protected function getContext(Throwable $throwable): array
    {
        return [
            'throw' => sprintf(
                '%s(%d): %s',
                $throwable->getFile(),
                $throwable->getLine(),
                get_class($throwable)
            ),
            'trace' => explode("\n", $throwable->getTraceAsString()),
        ];
    }

    /**
     * getExceptionStatusCode
     *
     * @param Throwable $throwable
     * @return integer
     */
    protected function getExceptionStatusCode(Throwable $throwable): int
    {
        if ($throwable instanceof AbstractException) {
            return $this->getValidHttpStatusCode($throwable->getCode());
        }

        if ($throwable instanceof HttpExceptionInterface) {
            return $throwable->getStatusCode();
        }

        if ($throwable instanceof InvalidArgumentException) {
            return $this->getValidHttpStatusCode($throwable->getCode(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->getValidHttpStatusCode($throwable->getCode());
    }

    /**
     * Check if $code is a valid Http Status
     * @param mixed $code
     * @param int   $default
     *
     * @return int
     */
    protected function getValidHttpStatusCode($code, int $default = JsonResponse::HTTP_INTERNAL_SERVER_ERROR): int
    {
        if (!isset(JsonResponse::$statusTexts[$code])) {
            return $default;
        }

        return $code;
    }
}
