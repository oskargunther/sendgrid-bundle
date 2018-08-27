<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 24.08.2018
 * Time: 15:31
 */
namespace OG\SendGridBundle\Provider;

use OG\SendGridBundle\Event\SendGridEvent;
use OG\SendGridBundle\Exception\AccessDeniedSendGridException;
use OG\SendGridBundle\Exception\BadRequestSendGridException;
use OG\SendGridBundle\Exception\UnauthorizedSendGridException;
use \SendGrid\Mail\Mail;
use OG\SendGridBundle\Exception\SendGridException;
use SendGrid\Mail\Personalization;
use SendGrid\Mail\To;
use SendGrid\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SendGridProvider
{
    /** @var \SendGrid */
    private $sendgrid;

    /** @var boolean */
    private $disableDelivery;

    /** @var boolean */
    private $webProfiler;

    /** @var EventDispatcherInterface  */
    private $eventDispatcher;

    /** @var mixed */
    private $redirectTo;

    /**
     * SendgridProvider constructor.
     * @param $apiKey string
     * @param $disableDelivery boolean
     * @param $webProfiler boolean
     * @param $redirectTo mixed
     * @param $eventDispatcher EventDispatcherInterface
     */
    public function __construct($apiKey, $disableDelivery, $webProfiler, $redirectTo, EventDispatcherInterface $eventDispatcher)
    {
        $this->sendgrid = new \SendGrid($apiKey);
        $this->disableDelivery = $disableDelivery;
        $this->webProfiler = $webProfiler;
        $this->redirectTo = $redirectTo;
        $this->eventDispatcher = $eventDispatcher;
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
     * @return string MessageId
     * @throws SendGridException
     */
    public function send(Mail $mail)
    {
        $this->eventDispatcher->dispatch(SendGridEvent::STARTED, new SendGridEvent($mail));
        if($this->disableDelivery) {
            $this->eventDispatcher->dispatch(SendGridEvent::FINISHED, new SendGridEvent($mail));
            return null;
        }

        try {
            $this->redirect($mail);
            $response = $this->sendgrid->send($mail);
            $this->checkResponse($response);
        } catch (\Exception $e) {
            $this->eventDispatcher->dispatch(SendGridEvent::FAILED, new SendGridEvent($mail));
            if($e instanceof SendGridException) {
                throw $e;
            }
            throw new SendGridException($e->getMessage());
        }

        $messageId = $this->getMessageId($response);
        $this->eventDispatcher->dispatch(SendGridEvent::FINISHED, new SendGridEvent($mail, $messageId));

        return $messageId;
    }

    private function redirect(Mail $mail)
    {
        if($this->redirectTo !== false) {
            $personalization = new Personalization();
            $personalization->addTo(new To($this->redirectTo));

            $refObject   = new \ReflectionObject($mail);
            $refProperty = $refObject->getProperty('personalization');
            $refProperty->setAccessible(true);
            $refProperty->setValue($mail, null);

            $mail->addPersonalization($personalization);
        }
    }

    private function getMessageId(Response $response)
    {
        return $response->headers(true)['X-Message-Id'];
    }

    private function checkResponse(Response $response)
    {
        if ($response->statusCode() == 401) {
            throw new UnauthorizedSendGridException($response->body());
        }

        if ($response->statusCode() == 403) {
            throw new AccessDeniedSendGridException($response->body());
        }

        if (preg_match('/5[0-9]{2}/', $response->statusCode())) {
            throw new \Exception($response->body());
        }

        if (preg_match('/4[0-9]{2}/', $response->statusCode())) {
            throw new BadRequestSendGridException($response->body());
        }
    }
}