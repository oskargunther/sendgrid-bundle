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

class SendGridProvider
{

    /** @var \SendGrid */
    private $sendgrid;

    /** @var boolean */
    private $disableDelivery;

    /** @var Mail[] */
    private $messages;

    /** @var boolean */
    private $webProfiler;

    /**
     * SendgridProvider constructor.
     * @param $apiKey string
     * @param $disableDelivery boolean
     * @param $webProfiler boolean
     */
    public function __construct($apiKey, $disableDelivery, $webProfiler)
    {
        $this->sendgrid = new \SendGrid($apiKey);
        $this->disableDelivery = $disableDelivery;
        $this->webProfiler = $webProfiler;
        $this->messages = [];
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
        if($this->webProfiler) {
            $this->messages[] = $mail;
        }

        if($this->disableDelivery) {
            return null;
        }

        try {
            $response = $this->sendgrid->send($mail);
            $this->checkResponse($response);
        } catch (\Exception $e) {
            throw new SendGridException($e->getMessage());
        }

        return $response;
    }

    public function getSentMessages()
    {
        return $this->messages;
    }

    private function checkResponse(Response $response)
    {
        if($response->statusCode() == 401) {
            throw new UnauthorizedSendGridException($response->body());
        }
    }
}