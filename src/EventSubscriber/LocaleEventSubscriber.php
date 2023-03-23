<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleEventSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale;
    /**
     * @param string $defaultLocale
     */
    public function __construct(string $defaultLocale = 'fr')
    {
        $this->defaultLocale = $defaultLocale;
    }
    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onRequestEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }
        if ($locale = $request->query->get('_locale')) {
            $request->setLocale($locale);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }
    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [['onRequestEvent', 20]],
        ];
    }
}