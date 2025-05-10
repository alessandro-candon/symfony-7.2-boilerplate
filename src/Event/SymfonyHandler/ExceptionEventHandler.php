<?php

declare(strict_types=1);

namespace App\Event\SymfonyHandler;

use App\Exception\Domain\DomainException;
use App\Exception\System\InvalidPayloadException;
use App\Exception\System\SystemException;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use function is_int;
use function Symfony\Component\String\u;

readonly class ExceptionEventHandler implements EventSubscriberInterface
{
    private const MINOR_PRIORITY = 2;

    public function __construct(
        private LoggerInterface $logger,
        private string $environment,
        private string $appVersion = '',
        private string $appName = ''
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->environment === 'dev') {
            return;
        }

        $request = $event->getRequest();

        $path = u($request->getPathInfo());

        if (!$path->match('/v[0-9]+\//m')) {
            return;
        }

        $exception = $event->getThrowable();

        $response = new JsonResponse();

        switch (true) {
            case $exception instanceof EntityNotFoundException:
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                break;
            case $exception instanceof InvalidPayloadException:
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent($exception->getMessage());
                break;
            case $exception instanceof HttpExceptionInterface:
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
                break;
            case $exception instanceof SystemException || $exception instanceof DomainException:
                $response->setStatusCode($exception->getCode());
                $response->setContent($exception->getMessage());
                break;
            case $exception instanceof AccessDeniedException:
                /** @phpstan-ignore function.alreadyNarrowedType */
                if (is_int($exception->getCode())) {
                    $response->setStatusCode($exception->getCode());
                }
                break;
            case $exception instanceof BadCredentialsException:
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $response->setContent($exception->getMessage());
                break;
            default:
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                if ($this->environment !== 'prod') {
                    $response->setContent($exception->getMessage());
                } else {
                    $response->setContent('Unhandled exception');
                }
                $response->setContent('Unhandled exception');
                break;
        }

        $response->headers->add([
            'content-type' => 'application/json; charset=utf-8',
            'application-version' => $this->appVersion,
            'application-name' => $this->appName,
        ]);

        $this->logger->error($exception->getMessage(), [
            'trace' => $exception->getTrace(),
            'file ' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        $event->setResponse($response);
        $event->allowCustomResponseCode();
    }

    /** @return array<string, array<string|int>> */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', self::MINOR_PRIORITY]];
    }
}
