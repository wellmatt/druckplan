<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once '/../../../vendor/Horde/Autoloader.php';
require_once '/../../../vendor/Horde/Autoloader/ClassPathMapper.php';
require_once '/../../../vendor/Horde/Autoloader/ClassPathMapper/Default.php';

/**
 * TODO: add default no reply address for system messages
 * TODO: add sender information based on ? User Object ?
 * TODO: fetch sender address + smtp information from sender
 * TODO: ? put send message in sent folder ? ( in case of system ? )
 */

/**
 * Class MailMessage
 */
class MailMessage{
    private $mailer;
    private $mail;

    public $mail_subject = '';
    public $mail_text = '';
    public $mail_to;
    public $mail_cc;
    public $mail_bcc;

    /**
     * MailMessage constructor.
     * @param $from
     * @param array $tos
     * @param array $ccs
     * @param array $bccs
     * @param array $attachments
     */
    public function __construct($from, $tos = [], $ccs = [], $bccs = [], $attachments = [])
    {
        // New Horde Horde_Mail_Transport_Smtp Object
        $this->mailer = new Horde_Mail_Transport_Smtp();

        // New Horde MIME_Mail Object
        $this->mail = new Horde_Mime_Mail();

        // Set the recipiants
        foreach ($tos as $recipiant) {
            $this->addHeader('TO', $recipiant);
        }

        // Set the cc recipiants
        foreach ($ccs as $recipiant) {
            $this->addHeader('CC', $recipiant);
        }

        // Set the bcc recipiants
        foreach ($bccs as $recipiant) {
            $this->addHeader('BCC', $recipiant);
        }

        // Set the attachments
        foreach ($attachments as $attach) {
            $this->addAttachment($attach);
        }
    }

    /**
     * Adds an attachment
     * @param $file
     * @param string $name
     */
    public function addAttachment($file, $name = '')
    {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $ftype = finfo_file($finfo, $file);
        finfo_close($finfo);
        $this->mail->addAttachment($file, $name, $ftype);
    }

    /**
     * Adds header information
     * @param $header
     * @param $value
     */
    public function addHeader($header, $value)
    {
        switch ($header){
            case 'TO':
                $this->mail->addHeader('TO', $value);
                break;
            case 'CC':
                $this->mail->addHeader('CC', $value);
                break;
            case 'BCC':
                $this->mail->addHeader('BCC', $value);
                break;
        }
    }

    /**
     * Sends the mail message
     */
    public function send()
    {
        // Set the header date
        $this->mail->addHeader('Date', date('r'));

        // Set the from address
        $this->mail->addHeader('From', $mail_from);

        // Set the subject of the mail
        $this->mail->addHeader('Subject', $this->mail_subject);

        // Set the text message body
        $this->mail->setHtmlBody($this->mail_text);

        // Send the message
        $this->mail->send($this->mailer);
    }
}