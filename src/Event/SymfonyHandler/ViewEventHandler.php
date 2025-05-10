<?php

declare(strict_types=1);

namespace App\Event\SymfonyHandler;

use App\Annotation\View;
use App\Pagination\PaginatedResult;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ViewEventHandler implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private string $appVersion = '',
        private string $appName = '',
    ) {
    }

    /**
     * Name getSubscribedEvents
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => 'onView'];
    }

    public function onView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if ($result instanceof Response) {
            return;
        }

        $request = $event->getRequest();

        $viewAttribute = $request->attributes->get(ControllerAttributeCollectorEventHandler::VIEW_ATTRIBUTE_KEY);

        if (! $viewAttribute instanceof View) {
            return;
        }

        $response = new Response();

        $response->setStatusCode($viewAttribute->statusCode);
        $response->headers->add([
            'content-type' => 'application/json; charset=utf-8',
            'application-version' => $this->appVersion,
            'application-name' => $this->appName,
        ]);

        if ($result instanceof PaginatedResult) {
            $dataToSerialize = $result->data;
            if ($result->enabled) {
                $response->headers->add([
                    'content-range' => $result->startRange . '-' . $result->endRange . '/' . $result->totalCount,
                    'accept-ranges' => $result->acceptedRanges,
                    'link'          => $result->getParsedLink(),
                ]);
            }
        } else {
            $dataToSerialize = $result;
        }

        if (!empty($viewAttribute->groups)) {
            $context = new SerializationContext();
            $context->setGroups($viewAttribute->groups);
            $context->setSerializeNull(true);
            $serialized = $this->serializer->serialize($dataToSerialize, 'json', $context);
        } else {
            $serialized = $this->serializer->serialize($dataToSerialize, 'json');
        }

        $response->setContent($serialized);

        $event->setResponse($response);
    }
}
