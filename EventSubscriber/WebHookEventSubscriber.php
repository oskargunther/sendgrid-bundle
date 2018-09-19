<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 26.08.2018
 * Time: 14:08
 */

namespace OG\SendGridBundle\EventSubscriber;


use OG\SendGridBundle\Event\WebHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class WebHookEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            WebHookEvent::BOUNCE => 'onBounce',
            WebHookEvent::CLICK => 'onClick',
            WebHookEvent::DEFERRED => 'onDeferred',
            WebHookEvent::DELIVERED => 'onDelivered',
            WebHookEvent::DROPPED => 'onDropped',
            WebHookEvent::GROUP_RESUBSCRIBE => 'onGroupResubscribe',
            WebHookEvent::GROUP_UNSUBSCRIBE => 'onGroupUnsubscribe',
            WebHookEvent::OPEN => 'onOpen',
            WebHookEvent::PROCESSED => 'onProcessed',
            WebHookEvent::SPAMREPORT => 'onSpamreport',
            WebHookEvent::UNSUBSCRIBE => 'onUnsubscribe',
        ];
    }

    abstract function onBounce(WebHookEvent $event);
    abstract function onClick(WebHookEvent $event);
    abstract function onDeferred(WebHookEvent $event);
    abstract function onDelivered(WebHookEvent $event);
    abstract function onDropped(WebHookEvent $event);
    abstract function onGroupResubscribe(WebHookEvent $event);
    abstract function onGroupUnsubscribe(WebHookEvent $event);
    abstract function onOpen(WebHookEvent $event);
    abstract function onProcessed(WebHookEvent $event);
    abstract function onSpamReport(WebHookEvent $event);
    abstract function onUnsubscribe(WebHookEvent $event);
}