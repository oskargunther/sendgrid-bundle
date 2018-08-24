<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 24.08.2018
 * Time: 15:31
 */
namespace OG\SendGridBundle\Provider;

use \SendGrid\Mail\Mail;
use OG\SendGridBundle\Exception\SendGridException;

class SendGridProvider
{

    /** @var \SendGrid */
    private $sendgrid;

    /** @var boolean */
    private $disableDelivery;

    /**
     * SendgridProvider constructor.
     * @param $apiKey string
     * @param $disableDelivery boolean
     */
    public function __construct($apiKey, $disableDelivery)
    {
        $this->sendgrid = new \SendGrid($apiKey);
        $this->disableDelivery = $disableDelivery;
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
        if($this->disableDelivery) {
            return null;
        }

        try {
            $response = $this->sendgrid->send($mail);
        } catch (\Exception $e) {
            throw new SendGridException($e->getMessage());
        }

        return $response;
    }
}