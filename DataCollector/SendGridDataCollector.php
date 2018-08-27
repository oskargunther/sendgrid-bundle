<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 24.08.2018
 * Time: 18:26
 */
namespace OG\SendGridBundle\DataCollector;

use OG\SendGridBundle\EventSubscriber\SendGridEventSubscriber;
use OG\SendGridBundle\Storage\MailLogStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Stopwatch\Stopwatch;

class SendGridDataCollector extends DataCollector
{
    /** @var boolean */
    private $webProfiler;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var MailLogStorage */
    private $mailLogStorage;

    public function __construct($webProfiler, Stopwatch $stopwatch, MailLogStorage $mailLogStorage)
    {
        $this->webProfiler = $webProfiler;
        $this->stopwatch = $stopwatch;
        $this->mailLogStorage = $mailLogStorage;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['messages'] = $this->mailLogStorage->getMails();
        $this->data['isEnabled'] = $this->webProfiler;

        try {
            $this->data['duration'] = $this->stopwatch->getEvent(SendGridEventSubscriber::STOPWATCH_EVENT)->getDuration();
        } catch (\Exception $e) {
            $this->data['duration'] = 0;
        }
    }

    public function reset()
    {
        $this->data = array();
    }

    public function getName()
    {
        return 'og_send_grid.data_collector';
    }

    public function getMessages()
    {
        return $this->data['messages'];
    }

    public function getIsEnabled()
    {
        return $this->data['isEnabled'];
    }

    public function getDuration()
    {
        return $this->data['duration'];
    }
}