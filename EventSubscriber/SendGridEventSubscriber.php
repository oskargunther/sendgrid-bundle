<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 26.08.2018
 * Time: 14:08
 */

namespace OG\SendGridBundle\EventSubscriber;


use OG\SendGridBundle\DataCollector\SendGridDataCollector;
use OG\SendGridBundle\Event\SendGridEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class SendGridEventSubscriber implements EventSubscriberInterface
{
    const STOPWATCH_EVENT = 'sendgrid';

    private $webProfiler;
    private $stopwatch;
    private $dataCollector;

    public function __construct($webProfiler, Stopwatch $stopwatch, SendGridDataCollector $dataCollector)
    {
        $this->webProfiler = $webProfiler;
        $this->stopwatch = $stopwatch;
        $this->dataCollector = $dataCollector;
    }

    public static function getSubscribedEvents()
    {
        return [
            SendGridEvent::FAILED => 'onFailed',
            SendGridEvent::STARTED => 'onStarted',
            SendGridEvent::FINISHED => 'onFinished',
        ];
    }

    public function onFailed(SendGridEvent $event)
    {
        if($this->webProfiler) {
            $this->stopwatch->stop(self::STOPWATCH_EVENT);
        }
    }

    public function onStarted(SendGridEvent $event)
    {
        if($this->webProfiler) {
            $this->stopwatch->start(self::STOPWATCH_EVENT);
        }
    }

    public function onFinished(SendGridEvent $event)
    {
        if($this->webProfiler) {
            $this->dataCollector->addMessage($event->getMail(), $event->getMessageId());
            $this->stopwatch->stop(self::STOPWATCH_EVENT);
        }
    }
}