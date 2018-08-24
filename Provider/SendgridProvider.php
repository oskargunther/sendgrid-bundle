<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 24.08.2018
 * Time: 15:31
 */
namespace OG\SendGridBundle\Provider;

use \SendGrid\Mail\Mail;

class SendGridProvider
{
    /** @var \SendGrid */
    private $sendgrid;

    /**
     * SendgridProvider constructor.
     * @param $apiKey string
     */
    public function __construct($apiKey)
    {
        $this->sendgrid = new \SendGrid($apiKey);
    }

    /**
     * @return \SendGrid\Mail\Mail
     */
    public function createMessage()
    {
        return new Mail();
    }

    public function send(Mail $mail)
    {
        $response = $this->sendgrid->send($mail);

        return $response;
    }
}