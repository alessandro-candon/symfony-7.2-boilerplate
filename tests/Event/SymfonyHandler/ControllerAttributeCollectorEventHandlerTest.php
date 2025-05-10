<?php
// phpcs:ignoreFile

namespace App\Tests\Event\SymfonyHandler;

use App\Annotation\View;
use App\Event\SymfonyHandler\ControllerAttributeCollectorEventHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerAttributeCollectorEventHandlerTest extends TestCase
{
    private HttpKernelInterface $httpKernelMock;

    protected function setUp(): void
    {
        $this->httpKernelMock = $this->createMock(HttpKernelInterface::class);
    }

    public function testShouldSubscribedEvents(): void
    {
        $this->assertEquals(
            [KernelEvents::CONTROLLER => 'onController'],
            ControllerAttributeCollectorEventHandler::getSubscribedEvents(),
        );
    }

    public function testOnControllerShouldNotSetAttribute(): void
    {
        $controllerAttributeCollector = new ControllerAttributeCollectorEventHandler();

        $request = new Request();

        $controller = static function (): void {
        };

        $event = new ControllerEvent(
            $this->httpKernelMock,
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $controllerAttributeCollector->onController($event);

        self::assertNull($request->attributes->get(ControllerAttributeCollectorEventHandler::VIEW_ATTRIBUTE_KEY));
    }

    #[DataProvider('provideControllers')]
    public function testOnControllerShouldSetAttribute($controller): void
    {
        $controllerAttributeCollector = new ControllerAttributeCollectorEventHandler();

        $request = new Request();

        $event = new ControllerEvent(
            $this->httpKernelMock,
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $controllerAttributeCollector->onController($event);

        self::assertInstanceOf(
            View::class,
            $request->attributes->get(ControllerAttributeCollectorEventHandler::VIEW_ATTRIBUTE_KEY)
        );
    }

    public static function provideControllers(): iterable
    {
        yield [
            #[View]
            static function (): void {
            },
        ];

        yield [
            [
                new TestViewController(),
                'testMethod',
            ],
        ];

        yield [
            new TestViewController(),
        ];
    }
}

class TestViewController
{
    #[View]
    public function testMethod(): void
    {
    }

    #[View]
    public function __invoke(): void
    {
    }
}
