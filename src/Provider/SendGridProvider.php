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

    /** @var Personalization[] */
    private $originalPersonalization;

    /** @var \ReflectionProperty */
    private $personalizationReflection;

    /** @var Personalization */
    private $redirectPersonalization;

    /**
     * SendgridProvider constructor.
     * @param $apiKey string
     * @param $disableDelivery boolean
     * @param $webProfiler boolean
     * @param $redirectTo mixed
     * @param $eventDispatcher EventDispatcherInterface
     */
    public function __construct(
        string $apiKey,
        bool $disableDelivery,
        bool $webProfiler,
        $redirectTo,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->sendgrid = new \SendGrid($apiKey);
        $this->disableDelivery = $disableDelivery;
        $this->webProfiler = $webProfiler;
        $this->redirectTo = $redirectTo;
        $this->eventDispatcher = $eventDispatcher;

        if($this->redirectTo !== false) {
            $this->redirectPersonalization = new Personalization();
            $this->redirectPersonalization->addTo(new To($this->redirectTo));

            $this->personalizationReflection = new \ReflectionProperty(Mail::class, 'personalization');
            $this->personalizationReflection->setAccessible(true);
        }
    }

    /**
     * @return \SendGrid\Mail\Mail
     */
    public function createMessage(): Mail
    {
        return new Mail();
    }

    /**
     * @param Mail $mail
     * @return string MessageId
     * @throws SendGridException
     */
    public function send(Mail $mail): ?string
    {
        $this->eventDispatcher->dispatch(SendGridEvent::STARTED, new SendGridEvent($mail));
        if($this->disableDelivery) {
            $this->eventDispatcher->dispatch(SendGridEvent::FINISHED, new SendGridEvent($mail));
            return null;
        }

        $response = $this->proceed($mail);

        $messageId = $this->getMessageId($response);
        $this->eventDispatcher->dispatch(SendGridEvent::FINISHED, new SendGridEvent($mail, $messageId));

        return $messageId;
    }

    private function proceed(Mail $mail): Response
    {
        try {
            $this->redirect($mail);

            $response = $this->sendgrid->send($mail);

            $this->reverseRedirection($mail);
            $this->checkResponse($response);

            return $response;

        } catch (\Exception $e) {
            $this->reverseRedirection($mail);
            $this->eventDispatcher->dispatch(SendGridEvent::FAILED, new SendGridEvent($mail));
            if($e instanceof SendGridException) {
                throw $e;
            }
            throw new SendGridException($e->getMessage());
        }
    }

    private function redirect(Mail $mail): void
    {
        if($this->redirectTo !== false) {
            $this->originalPersonalization = $mail->getPersonalizations();

            $this->personalizationReflection->setValue($mail, [$this->redirectPersonalization]);
        }
    }

    private function reverseRedirection(Mail $mail): void
    {
        if($this->redirectTo !== false) {
            $this->personalizationReflection->setValue($mail, $this->originalPersonalization);
        }
    }

    private function getMessageId(Response $response): ?string
    {
        try {
            return $response->headers(true)['X-Message-Id'];
        } catch (\Exception $e) {
            throw new SendGridException('X-Message-Id header not found in SendGrid API response');
        }
    }

    private function checkResponse(Response $response): void
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