<?
use Zend\Mail;
use Zend\Mail\Headers;
use Zend\Mail\Storage;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\SmtpOptions;

class MailModel {

	private $_emailAddress;
	private $_accountData;
	private $_account;
	private $_folder;
	
	function __construct($emailAddress, $folder) {
		$this->_emailAddress = $emailAddress;
		$this->_folder = $folder;

		// Connect to the mail account using the specified credentials and settings.
		$this->_accountData = array(
				"host"		=>	$this->_emailAddress->getHost(),
				"user"		=> 	$this->_emailAddress->getAddress(),
				"password"	=>	$this->_emailAddress->getPassword(),
				"folder"	=>	$this->_folder
		);
		
		if($this->_emailAddress->getUseSSL() == 1) {
			$this->_accountData["ssl"] = "SSL";
		}
		
		if($this->_emailAddress->getPort() != 0) {
			$this->_accountData["port"] = $this->_emailAddress->getPort();
		}
		
		try {
			$this->_account = new Zend\Mail\Storage\Imap($this->_accountData);
		} catch(Exception $e) {}
	}
	
	public function sendMail($mail) {
		$transport = new SendmailTransport();
		$transport->send($mail);
	}
	
	public function getAccountData() {
		return $this->_accountData;
	}
	
	public function getAccount() {
		return $this->_account;
	}
	
	public function getFolder() {
		return $this->_folder;
	}
}
?>
