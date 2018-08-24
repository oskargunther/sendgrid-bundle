<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 24.08.2018
 * Time: 15:31
 */
namespace OG\SendGridBundle\Provider;

use OG\SendGridBundle\Exception\UnauthorizedSendGridException;
use \SendGrid\Mail\Mail;
use OG\SendGridBundle\Exception\SendGridException;
use SendGrid\Response;
use Symfony\Component\Stopwatch\Stopwatch;

class SendGridProvider
{
    const EVENT = 'sendgrid';

    /** @var \SendGrid */
    private $sendgrid;

    /** @var boolean */
    private $disableDelivery;

    /** @var Mail[] */
    private $messages;

    /** @var boolean */
    private $webProfiler;

    /** @var Stopwatch */
    private $watch;

    /**
     * SendgridProvider constructor.
     * @param $apiKey string
     * @param $disableDelivery boolean
     * @param $webProfiler boolean
     * @param $watch Stopwatch
     */
    public function __construct($apiKey, $disableDelivery, $webProfiler, Stopwatch $watch)
    {
        $this->sendgrid = new \SendGrid($apiKey);
        $this->disableDelivery = $disableDelivery;
        $this->webProfiler = $webProfiler;
        $this->messages = [];
        if($webProfiler) {
            $this->watch = $watch;
        }
    }

    /**
     * @return \SendGrid\Mail\Mail
     */
    public function createMessage()
    {
        return new Mail();
    }

    /**
     * @param Mail $mail
     * @return \SendGrid\Response|null
     * @throws SendGridException
     */
    public function send(Mail $mail)
    {
        $this->start();
        if($this->webProfiler) {
            $this->messages[] = $mail;
        }

        if($this->disableDelivery) {
            $this->stop();
            return null;
        }

        try {
            $response = $this->sendgrid->send($mail);
            $this->checkResponse($response);
        } catch (\Exception $e) {
            $this->stop();

            if($e instanceof SendGridException) {
                throw $e;
            }
            throw new SendGridException($e->getMessage());
        }

        $this->stop();
        return $response;
    }

    public function getSentMessages()
    {
        return $this->messages;
    }

    private function start()
    {
        if($this->webProfiler) {
            $this->watch->start(self::EVENT);
        }
    }

    private function stop()
    {
        if($this->webProfiler) {
            $event = $this->watch->stop(self::EVENT);
            $event->ensureStopped();
        }
    }

    private function checkResponse(Response $response)
    {
        if($response->statusCode() == 401) {
            throw new UnauthorizedSendGridException($response->body());
        }
    }
}