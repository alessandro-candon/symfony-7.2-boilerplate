<?php

namespace App\Tests\Event\SymfonyHandler;

use App\Annotation\View;
use App\Event\SymfonyHandler\ControllerAttributeCollectorEventHandler;
use App\Event\SymfonyHandler\ViewEventHandler;
use App\Pagination\PaginatedResult;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewEventHandlerTest extends TestCase
{
    private ViewEventHandler $viewHandler;
    private SerializerInterface|MockObject $serializer;
    private HttpKernelInterface $httpKernel;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->viewHandler = new ViewEventHandler($this->serializer, '', '');
        $this->httpKernel = $this->createMock(HttpKernelInterface::class);
    }

    public function testShouldSubscribedEvents(): void
    {
        $this->assertEquals(
            [KernelEvents::VIEW => 'onView'],
            ViewEventHandler::getSubscribedEvents(),
        );
    }

    public function testOnViewShouldDoNothingIfControllerResultIsAResponse(): void
    {
        $event = new ViewEvent(
            $this->httpKernel,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->viewHandler->onView($event);

        self::assertNull($event->getResponse());
    }

    public function testOnViewShouldDoNothingIfIsNotAInstanceOfView(): void
    {
        $event = new ViewEvent(
            $this->httpKernel,
            (new Request()),
            HttpKernelInterface::MAIN_REQUEST,
            [],
        );

        $this->viewHandler->onView($event);

        self::assertNull($event->getResponse());
    }

    public function testOnViewShouldDoNothingIfControllerResultIsAResponseAndViewAttributeIsSet(): void
    {
        $request = new Request();
        $request->attributes->set('_view', new View());

        $event = new ViewEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );
        $this->viewHandler->onView($event);

        self::assertNull($event->getResponse());
    }

    public function testOnViewShouldSetResponse(): void
    {
        $request = new Request();
        $request->attributes->set(ControllerAttributeCollectorEventHandler::VIEW_ATTRIBUTE_KEY, new View());

        $event = new ViewEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            [],
        );

        $this->serializer->method('serialize')->willReturn('{}');

        $this->viewHandler->onView($event);

        self::assertNotNull($event->getResponse());

        self::assertEquals('{}', $event->getResponse()->getContent());

        self::assertEquals(
            'application/json; charset=utf-8',
            $event->getResponse()->headers->get('content-type'),
        );
    }

    public function testOnViewShouldSetDynamicGroups(): void
    {
        $request = new Request(['groups' => 'full,index,show_id']);
        $request->attributes->set(ControllerAttributeCollectorEventHandler::VIEW_ATTRIBUTE_KEY, new View());

        $event = new ViewEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            [],
        );

        $context = new SerializationContext();
        $context->setGroups(['full', 'index', 'show_id']);
        $context->setSerializeNull(true);

        $this->serializer->method('serialize')->willReturn('{}');

        $this->viewHandler->onView($event);

        self::assertNotNull($event->getResponse());

        self::assertEquals('{}', $event->getResponse()->getContent());

        self::assertEquals(
            'application/json; charset=utf-8',
            $event->getResponse()->headers->get('content-type'),
        );
    }

    public function testOnViewShouldSetResponseOnPaginatedResultDTO(): void
    {
        $request = new Request();
        $request->attributes->set(ControllerAttributeCollectorEventHandler::VIEW_ATTRIBUTE_KEY, new View());

        $paginatedResultDTO = new PaginatedResult();
        $paginatedResultDTO->acceptedRanges = 'ranges';

        $event = new ViewEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $paginatedResultDTO,
        );

        $this->serializer->method('serialize')->willReturn('{}');

        $this->viewHandler->onView($event);

        self::assertNotNull($event->getResponse());

        self::assertEquals('{}', $event->getResponse()->getContent());

        self::assertEquals(
            'application/json; charset=utf-8',
            $event->getResponse()->headers->get('content-type'),
        );

        self::assertEquals(
            'ranges',
            $event->getResponse()->headers->get('accept-ranges'),
        );

        self::assertEquals(
            '0-20/0',
            $event->getResponse()->headers->get('content-range'),
        );
    }

    public function testOnViewShouldSetResponseOnPaginatedResultDTODisabled(): void
    {
        $request = new Request();
        $request->attributes->set(ControllerAttributeCollectorEventHandler::VIEW_ATTRIBUTE_KEY, new View());

        $paginatedResultDTO = new PaginatedResult();
        $paginatedResultDTO->acceptedRanges = 'ranges';
        $paginatedResultDTO->enabled = false;

        $event = new ViewEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $paginatedResultDTO,
        );

        $this->serializer->method('serialize')->willReturn('{}');

        $this->viewHandler->onView($event);

        $this->assertNotNull($event->getResponse());

        $this->assertEquals('{}', $event->getResponse()->getContent());

        $this->assertEquals(
            'application/json; charset=utf-8',
            $event->getResponse()->headers->get('content-type'),
        );

        $this->assertNull($event->getResponse()->headers->get('ranges'));

        $this->assertNull($event->getResponse()->headers->get('content-range'));
    }
}
