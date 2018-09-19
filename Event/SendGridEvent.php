<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 26.08.2018
 * Time: 13:48
 */

namespace OG\SendGridBundle\Event;


use SendGrid\Mail\Mail;
use Symfony\Component\EventDispatcher\Event;

class SendGridEvent extends Event
{
    const STARTED = 'sendgrid.started';
    const FAILED = 'sendgrid.failed';
    const FINISHED = 'sendgrid.finished';

    /** @var Mail */
    private $mail;

    /** @var string */
    private $messageId;

    public function __construct(Mail $mail = null, $messageId = null)
    {
        $this->mail = $mail;
        $this->messageId = $messageId;
    }

    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }
}