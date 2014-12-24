<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.03.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

error_reporting(-1);
ini_set('display_errors', 1);

session_start();

chdir('../../../../');



require_once 'config.php';

require_once $_BASEDIR . '/libs/modules/calculation/calculation.class.php';
require_once $_BASEDIR . '/libs/basic/mysql.php';
require_once $_BASEDIR . '/libs/basic/globalFunctions.php';
require_once $_BASEDIR . '/libs/basic/user/user.class.php';
require_once $_BASEDIR . '/libs/basic/groups/group.class.php';
require_once $_BASEDIR . '/libs/basic/clients/client.class.php';
require_once $_BASEDIR . '/libs/basic/translator/translator.class.php';
require_once $_BASEDIR . '/libs/basic/countries/country.class.php';

require_once $_BASEDIR . '/libs/basic/countries/country.class.php';
require_once $_BASEDIR . '/libs/basic/debug.php';
require_once $_BASEDIR . '/libs/basic/license/license.class.php';

use Zend\Mail;
use Zend\Mail\Headers;
use Zend\Mail\Storage;
use Zend\Mail\Storage\Imap;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;



$DB = new DBMysql();
$DB->connect($_CONFIG->db);
$_DEBUG = new Debug();
$_LICENSE = new License();


var_dump($_LICENSE->dump());

require_once "mailModel.class.php";


$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

$messsageId = (int) $_GET['id'];

$userEmailAdresses = $_USER->getEmailAddresses();
$emailId = getRequestVarOr("emailId", null);
if($emailId == null) {
    foreach($userEmailAdresses as $emailAddress) {
        if($emailId == null) $emailId = $emailAddress->getId();
    }
}
if($emailId == null) {
    exit(false);
}

$emailIds = array();
foreach($userEmailAdresses as $emailAddress) {
    $emailIds[$emailAddress->getAddress()] = $emailAddress->getId();
}

// Fetch all folders/messages for each registered account.
foreach($userEmailAdresses as $emailAddress) {
    if($emailAddress->getId() != $emailId) {
        continue;
    }

    // Create a new MailModel instance.
    $mailModel = new MailModel($emailAddress, $selectedFolder);
    if(!$mailModel->getAccount()) {
        $error = '<div>Verbindung zu '.$emailAddress->getAddress().' konnte nicht hergestellt werden.</div>';
        exit($error);
    }
}

$messageIds = array();
foreach($messageIds as $messageId) {
    $number = null;
    try {
        $number = $mailModel->getAccount()->getNumberByUniqueId($messageId);
    } catch(Exception $e) {
        $error = "Die Nachricht(en) konnte nicht verschoben werden.";
    }

    if($number != null) {
        $copied = true;
        try {
            $mailModel->getAccount()->copyMessage($number, $destinationFolder);
        } catch(Exception $e) {
            $copied = false;
            $error = "Die Nachricht(en) konnte nicht verschoben werden.";
        }
        if($copied) {
            try {
                $mailModel->getAccount()->removeMessage($number);
            } catch(Exception $e) {
                $error = "Die Nachricht(en) konnte nicht verschoben werden.";
            }
        }
    } else {
        $error = "Die Nachricht(en) konnte nicht verschoben werden.";
    }

    $mailModel->getAccount()->noop();
}