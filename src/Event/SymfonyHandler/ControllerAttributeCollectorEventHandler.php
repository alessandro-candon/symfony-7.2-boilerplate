<?php

declare(strict_types=1);

namespace App\Event\SymfonyHandler;

use App\Annotation\View;
use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use function is_array;

class ControllerAttributeCollectorEventHandler implements EventSubscriberInterface
{
    public const VIEW_ATTRIBUTE_KEY = '_view_attribute';

    private const CONTROLLER_ATTRIBUTES = [
        self::VIEW_ATTRIBUTE_KEY => View::class,
    ];

    /**
     * Name getSubscribedEvents
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'onController'];
    }

    /** @throws ReflectionException */
    public function onController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        $request = $event->getRequest();

        if ($controller instanceof Closure) {
            $reflector = new ReflectionFunction($controller);
        } elseif (is_array($controller)) {
            $reflectorClass = new ReflectionClass($controller[0]);
            $reflector = $reflectorClass->getMethod($controller[1]);
        } else {
            $reflectorClass = new ReflectionClass($controller);
            $reflector = $reflectorClass->getMethod('__invoke');
        }

        foreach (self::CONTROLLER_ATTRIBUTES as $controllerAttributeKey => $controllerAttributeValue) {
            foreach ($reflector->getAttributes($controllerAttributeValue) as $reflectionAttribute) {
                $attribute = $reflectionAttribute->newInstance();
                $request->attributes->set($controllerAttributeKey, $attribute);
            }
        }
    }
}
