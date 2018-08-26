<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 24.08.2018
 * Time: 18:26
 */
namespace OG\SendGridBundle\DataCollector;

use OG\SendGridBundle\EventSubscriber\SendGridEventSubscriber;
use SendGrid\Mail\Attachment;
use SendGrid\Mail\Content;
use SendGrid\Mail\EmailAddress;
use SendGrid\Mail\Mail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Stopwatch\Stopwatch;

class SendGridDataCollector extends DataCollector
{
    /** @var boolean */
    private $webProfiler;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var array */
    private $messages;

    public function __construct($webProfiler, Stopwatch $stopwatch)
    {
        $this->webProfiler = $webProfiler;
        $this->stopwatch = $stopwatch;
        $this->messages = [];
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['messages'] = $this->messages;
        $this->data['isEnabled'] = $this->webProfiler;

        try {
            $this->data['duration'] = $this->stopwatch->getEvent(SendGridEventSubscriber::STOPWATCH_EVENT)->getDuration();
        } catch (\Exception $e) {
            $this->data['duration'] = 0;
        }
    }

    private function transform(Mail $mail, $messageId = '')
    {
        return [
            'subject' => $mail->getGlobalSubject()->getSubject(),
            'from' => $this->formatAddress($mail->getFrom()),
            'tos' => $this->getRecipients($mail, 'tos'),
            'bccs' => $this->getRecipients($mail, 'bccs'),
            'ccs' => $this->getRecipients($mail, 'ccs'),
            'contents' => array_map(function (Content $content) {
                return [
                    'type' => $content->getType(),
                    'content' => $content->getValue(),
                ];
            }, $mail->getContents()),
            'attachments' => array_map(function(Attachment $attachment) {
                return [
                    'filename' => $attachment->getFilename(),
                    'mime' => $attachment->getType(),
                    'cid' => $attachment->getContentID(),
                    'disposition' => $attachment->getDisposition()
                ];
            }, $mail->getAttachments()),
            'messageId' => $messageId,
        ];
    }

    private function getRecipients(Mail $mail, $type)
    {
        $recipients = [];

        foreach ($mail->getPersonalizations() as $personalization) {
            if(empty($personalization->{'get'.$type}())) {
                continue;
            }
            /** @var EmailAddress $address */
            foreach ($personalization->{'get'.$type}() as $address) {
                $recipients[] = $this->formatAddress($address);
            }
        }

        return $recipients;
    }

    private function formatAddress(EmailAddress $address)
    {
        return $address->getName() . ' <' . $address->getEmailAddress() . '>';
    }

    public function reset()
    {
        $this->data = array();
    }

    public function getName()
    {
        return 'og_send_grid.data_collector';
    }

    public function getMessages()
    {
        return $this->data['messages'];
    }

    public function getIsEnabled()
    {
        return $this->data['isEnabled'];
    }

    public function getDuration()
    {
        return $this->data['duration'];
    }

    public function addMessage(Mail $mail, $messageId = null)
    {
        $this->messages[] = $this->transform($mail, $messageId);
    }
}