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
        try {
            $response = $this->sendgrid->send($mail);
        } catch (\Exception $e) {
            throw new SendGridException($e->getMessage());
        }

        return $response;
    }
}