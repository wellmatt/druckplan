<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/perferences/perferences.class.php';
require_once 'vendor/PEAR/Net/SMTP.php';
require_once 'vendor/PEAR/Net/Socket.php';
require_once 'vendor/Horde/Autoloader.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper/Default.php';
$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default('vendor'));
$autoloader->registerAutoloader();


/**
 * Class MailMessage
 */
class MailMessage{
    private $mailer;
    private $mail;

    public $mail_from;
    public $mail_subject = '';
    public $mail_text = '';
    public $mail_to;
    public $mail_cc;
    public $mail_bcc;
    public $mail_attachments;

    public $imap_host;
    public $imap_port;
    public $imap_user;
    public $imap_pass;

    /**
     * MailMessage constructor.
     * @param Emailaddress $from can be Emailaddress to send from specific user or anything else to send system message
     * @param array $tos
     * @param string $subject
     * @param string $text
     * @param array $ccs
     * @param array $bccs
     * @param array $attachments
     */
    public function __construct($from, $tos = [], $subject, $text, $ccs = [], $bccs = [], $attachments = [])
    {
        if (is_a($from,'Emailaddress')){
            if ($from->getSmtpSsl() == 1){
                $ssl = 'ssl://';
            } else {
                $ssl = '';
            }
            $this->mail_from = $from->getAddress();
            $smtp_params = array(
                'host' => $ssl.$from->getSmtpHost(),
                'port' => $from->getSmtpPort(),
                'username' => $from->getSmtpUser(),
                'password' => $from->getSmtpPassword(),
                'tls' => $from->getSmtpTls(),
                'debug' => false,
                'auth' => true,
                'timeout' => 300
            );
            $this->imap_host = $from->getHost();
            $this->imap_port = $from->getPort();
            $this->imap_user = $from->getLogin();
            $this->imap_pass = $from->getPassword();
            $mail_from = $from->getAddress();
        } else {
            $perf = new Perferences();
            $perf->getSmtpAddress();
            if ($perf->getSmtpSsl() == 1){
                $ssl = 'ssl://';
            } else {
                $ssl = '';
            }
            $smtp_params = array(
                'host' => $ssl.$perf->getSmtpHost(),
                'port' => $perf->getSmtpPort(),
                'username' => $perf->getSmtpUser(),
                'password' => $perf->getSmtpPassword(),
                'tls' => $perf->getSmtpTls(),
                'debug' => false,
                'auth' => true,
                'timeout' => 300
            );
            $this->imap_host = $perf->getImapHost();
            $this->imap_port = $perf->getImapPort();
            $this->imap_user = $perf->getImapUser();
            $this->imap_pass = $perf->getImapPassword();
            if ($perf->getMailSender() == '')
                $mail_from = $perf->getSmtpAddress();
            else {
                $mail_from = $perf->getMailSender()."<".$perf->getSmtpAddress().">";
            }
        }

        $tos = array_filter($tos);
        $ccs = array_filter($ccs);
        $bccs = array_filter($bccs);
        $this->mail_subject = $subject;
        $this->mail_text = $text;

        // New Horde Horde_Mail_Transport_Smtp Object
        $this->mailer = new Horde_Mail_Transport_Smtp($smtp_params);
//        dd($this->mailer->getSMTPObject());

        // New Horde MIME_Mail Object
        $this->mail = new Horde_Mime_Mail();

        $this->addHeader('From', $mail_from);

        // Set the header date
        $this->mail->addHeader('Date', date('r'));

        // Set the subject of the mail
        $this->mail->addHeader('Subject', $this->mail_subject);

        // Set the text message body
        $this->mail->setHtmlBody($this->mail_text);

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
        foreach ($attachments as $name => $attach) {
            $this->addAttachment($attach, $name);
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
        $this->mail_attachments[$name] = $file;
    }

    /**
     * Adds header information
     * @param $header
     * @param $value
     */
    public function addHeader($header, $value)
    {
        switch ($header){
            case 'From':
                $this->mail_from = $value;
                $this->mail->addHeader('From', $value);
                break;
            case 'TO':
                $this->mail_to[] = $value;
                $this->mail->addHeader('TO', $value, false);
                break;
            case 'CC':
                $this->mail_cc[] = $value;
                $this->mail->addHeader('CC', $value, false);
                break;
            case 'BCC':
                $this->mail_bcc[] = $value;
                $this->mail->addHeader('BCC', $value, false);
                break;
        }
    }

    /**
     * Sends the mail message
     */
    public function send()
    {
        // Send the message
//        prettyPrint($this->mailer);
//        prettyPrint($this->mail);
        $this->mail->send($this->mailer);
        $this->storeSent();
    }

    public function storeDraft()
    {
        // New Horde MIME_Mail Object
        $mail = $this->mail;

        // Add the file as an attachment, set the file name and what kind of file it is.
        $mime_parts = Array();
        foreach ($this->mail_attachments as $name => $attach) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $ftype = finfo_file($finfo, 'libs/modules/attachment/files/'.$attach);
            finfo_close($finfo);

            $part = new Horde_Mime_Part();
            $part->setType($ftype);
            $part->setCharset('us-ascii');
            $part->setDisposition('attachment');
            $part->setContents(file_get_contents($attach));
            $part->setName($name);
            $part->setTransferEncoding('base64',false);
            $mime_parts[] = $part;
        }

        try {
            /* Connect to an IMAP server.
             *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
             *     connect to a POP3 server instead. */
            $client = new Horde_Imap_Client_Socket(array(
                'username' => $this->imap_user,
                'password' => $this->imap_pass,
                'hostspec' => $this->imap_host,
                'port' => $this->imap_port,
                'secure' => 'ssl',
                'cache' => array(
                    'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                        'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                            'dir' => '/tmp/hordecache'
                        )))
                    ))
                )
            ));

            $mail->addHeaderOb(Horde_Mime_Headers_MessageId::create());
            $mail->addHeaderOb(Horde_Mime_Headers_UserAgent::create());
            $mail->addHeaderOb(Horde_Mime_Headers_Date::create());

            $htmlBody = new Horde_Mime_Part();
            $htmlBody->setType('text/html');
            $htmlBody->setCharset('UTF-8');
            $htmlBody->setContents($this->mail_text);
            $htmlBody->setDescription(Horde_Mime_Translation::t("HTML Version of Message"));
            $htmlBody->toString();

            $plainText = Horde_Text_Filter::filter($this->mail_text, 'Html2text', array('charset' => 'UTF-8', 'wrap' => false));

            $textBody = new Horde_Mime_Part();
            $textBody->setType('text/plain');
            $textBody->setCharset('UTF-8');
            $textBody->setContents($plainText);
            $textBody->setDescription(Horde_Mime_Translation::t("Plaintext Version of Message"));
            $flowed = new Horde_Text_Flowed($textBody->getContents(), $textBody->getCharset());
            $flowed->setDelSp(true);
            $textBody->setContentTypeParameter('format', 'flowed');
            $textBody->setContentTypeParameter('DelSp', 'Yes');
            $textBody->setContents($flowed->toFlowed());
            $textBody->toString();

            $body = new Horde_Mime_Part();
            $body->setType('multipart/alternative');
            $body->addPart($textBody);
            $body->addPart($htmlBody);
            $body->setTransferEncoding('binary',false);
            $body->toString();

            $basepart = new Horde_Mime_Part();
            $basepart->setType('multipart/mixed');
            $basepart->addPart($body);
            $basepart->isBasePart(true);

            if (count($mime_parts)) {
                foreach ($mime_parts as $mime_part) {
                    $basepart->addPart($mime_part);
                }
            }
            $basepart->setHeaderCharset('UTF-8');
            $basepart->setMimeId("1");
            $basepart->addMimeHeaders();
            $basepart->buildMimeIds($basepart->getMimeId());
            $basepart->toString();
            $boundary_base = $basepart->getContentTypeParameter('boundary');

            $mail->setBasePart($basepart);
            $mail->removeHeader('MIME-Version');



            $mail_header = new Horde_Mime_Headers_ContentParam_ContentType('Content-Type','multipart/mixed');
            $mail_header->unserialize(
                serialize(
                    array(
                        '_params'=> Array(
                            'boundary' => $boundary_base,
                        ),
                        '_values'=> Array(
                            'multipart/mixed',
                        )
                    )
                )
            );
            $mail->addHeaderOb($mail_header);

            $message_array = Array( Array("data"=>Array(Array("t"=>"text","v"=>$mail->getRaw(false)))) );
            $client->append("contilas-draft", $message_array, Array("create"=>true));
        } catch (Horde_Imap_Client_Exception $e) {}
    }

    public function storeSent()
    {
        try {
            /* Connect to an IMAP server.
             *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
             *     connect to a POP3 server instead. */
            $client = new Horde_Imap_Client_Socket(array(
                'username' => $this->imap_user,
                'password' => $this->imap_pass,
                'hostspec' => $this->imap_host,
                'port' => $this->imap_port,
                'secure' => 'ssl',
                'cache' => array(
                    'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                        'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                            'dir' => '/tmp/hordecache'
                        )))
                    ))
                )
            ));
            $message_array = Array( Array("data"=>Array(Array("t"=>"text","v"=>$this->mail->getRaw(false)))) );
            $client->append("contilas-sent", $message_array, Array("create"=>true));
        } catch (Horde_Imap_Client_Exception $e) {}
    }
}