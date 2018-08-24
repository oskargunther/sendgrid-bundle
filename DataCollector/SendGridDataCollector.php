<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 24.08.2018
 * Time: 18:26
 */
namespace OG\SendGridBundle\DataCollector;

use OG\SendGridBundle\Provider\SendGridProvider;
use SendGrid\Mail\Attachment;
use SendGrid\Mail\EmailAddress;
use SendGrid\Mail\Mail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SendGridDataCollector extends DataCollector
{
    /** @var SendGridProvider */
    private $sendGridProvider;

    /** @var boolean */
    private $webProfiler;

    public function __construct(SendGridProvider $sendGridProvider, $webProfiler)
    {
        $this->sendGridProvider = $sendGridProvider;
        $this->webProfiler = $webProfiler;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['messages'] = array_map(function(Mail $mail) {
            return [
                'subject' => $mail->getGlobalSubject()->getSubject(),
                'from' => $this->formatAddress($mail->getFrom()),
                'tos' => $this->getRecipients($mail, 'tos'),
                'bccs' => $this->getRecipients($mail, 'bccs'),
                'ccs' => $this->getRecipients($mail, 'ccs'),
                'contents' => $mail->getContents(),
                'attachments' => array_map(function(Attachment $attachment) {
                    return [
                        'filename' => $attachment->getFilename(),
                        'mime' => $attachment->getType(),
                        'cid' => $attachment->getContentID(),
                        'disposition' => $attachment->getDisposition()
                    ];
                }, $mail->getAttachments()),
            ];
        }, $this->sendGridProvider->getSentMessages());

        $this->data['isEnabled'] = $this->webProfiler;
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

}