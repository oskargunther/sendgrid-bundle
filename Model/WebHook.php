<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 26.08.2018
 * Time: 13:59
 */
namespace OG\SendGridBundle\Model;


class WebHook
{
    /** @var string */
    private $email;

    /** @var int */
    private $timestamp;

    /** @var string */
    private $smtpId;

    /** @var string */
    private $event;

    /** @var string|string[] */
    private $category;

    /** @var  */
    private $sgEventId;

    /** @var string */
    private $sgMessageId;

    /** @var string */
    private $response;

    /** @var string */
    private $attempt;

    /** @var string */
    private $ip;

    /** @var string */
    private $userAgent;

    /** @var string */
    private $url;

    /** @var string */
    private $status;

    /** @var mixed */
    private $reason;

    /**
     * @var string
     * Full message received from SendGrid
     */
    private $messageBody;

    public function __construct(array $data)
    {
        $this->setData('email', $data, 'email');
        $this->setData('timestamp', $data, 'timestamp');
        $this->setData('smtpId', $data, 'smtp-id');
        $this->setData('event', $data, 'event');
        $this->setData('category', $data, 'category');
        $this->setData('sgEventId', $data, 'sg_event_id');
        $this->setData('sgMessageId', $data, 'sg_message_id');
        $this->setData('response', $data, 'response');
        $this->setData('attempt', $data, 'attempt');
        $this->setData('ip', $data, 'ip');
        $this->setData('userAgent', $data, 'useragent');
        $this->setData('url', $data, 'url');
        $this->setData('status', $data, 'status');
        $this->setData('reason', $data, 'reason');

        $this->messageBody = $data;
    }

    private function setData($property, array &$data, $dataProperty)
    {
        $this->{$property} = isset($data[$dataProperty]) ? $data[$dataProperty] : null;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getSmtpId(): string
    {
        return $this->smtpId;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return string|string[]
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getSgEventId()
    {
        return $this->sgEventId;
    }

    /**
     * @return string
     */
    public function getSgMessageId(): string
    {
        return $this->sgMessageId;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getAttempt(): string
    {
        return $this->attempt;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getMessageBody(): string
    {
        return $this->messageBody;
    }

}