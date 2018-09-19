<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 26.08.2018
 * Time: 13:48
 */
namespace OG\SendGridBundle\Event;


use OG\SendGridBundle\Model\WebHook;
use Symfony\Component\EventDispatcher\Event;

class WebHookEvent extends Event
{
    const PROCESSED = 'sendgrid.webhook.processed';
    const DEFERRED = 'sendgrid.webhook.deferred';
    const DELIVERED = 'sendgrid.webhook.delivered';
    const OPEN = 'sendgrid.webhook.open';
    const CLICK = 'sendgrid.webhook.click';
    const BOUNCE = 'sendgrid.webhook.bounce';
    const DROPPED = 'sendgrid.webhook.dropped';
    const SPAMREPORT = 'sendgrid.webhook.spamreport';
    const UNSUBSCRIBE = 'sendgrid.webhook.unsubscribe';
    const GROUP_UNSUBSCRIBE = 'sendgrid.webhook.group_unsubscribe';
    const GROUP_RESUBSCRIBE = 'sendgrid.webhook.group_resubscribe';

    /** @var WebHook */
    private $webHook = null;

    public function __construct(WebHook $webHook)
    {
        $this->webHook = $webHook;
    }

    public function getWebHook(): WebHook
    {
        return $this->webHook;
    }

    static public function getNamePrefix(): string
    {
        return 'sendgrid.webhook';
    }

}