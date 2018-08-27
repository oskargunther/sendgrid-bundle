<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 27.08.2018
 * Time: 09:11
 */

namespace OG\SendGridBundle\Storage;


use SendGrid\Mail\Attachment;
use SendGrid\Mail\Content;
use SendGrid\Mail\EmailAddress;
use SendGrid\Mail\Mail;

class MailLogStorage
{
    /**
     * @var array
     */
    private $mails;

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

    public function addMail(Mail $mail, $messageId = '')
    {
        $this->mails[] = $this->transform($mail, $messageId);
    }

    public function getMails()
    {
        return $this->mails;
    }
}