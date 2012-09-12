<?php
require_once APPLICATION_PATH."code/core/adapters/Adapter.class.php";

class ImapAdapter extends Adapter
{
	private $_connection;
	private $_username;
	private $_password;
	private $_connection_alive;
	private $_mailserver;
	private $_connection_string;
	private $_ssl;
	private $_port;
	const LOG = false;

	const SINGLETON_NAMESPACE = 'emails/';

	public function __construct($conn_name, $host,$port, $login, $password, $security)
	{
		parent::__construct(self::SINGLETON_NAMESPACE, $conn_name);

		$this->_username = $login;
		$this->_password = $password;
		$this->_connection_alive = false;
		$this->_mailserver = $host;
		$this->_ssl = $security;
		$this->_port = intval($port);
	}

	private function openConnection()
	{
		try {
				if($this->_ssl == '')
					$this->_connection = new Zend_Mail_Storage_Imap(
										array(	'host'     => $this->_mailserver,
                                         		'user'     =>  $this->_username,
                                         		'password' => $this->_password,
												'port' => $this->_port
									));
				else 
					$this->_connection = new Zend_Mail_Storage_Imap(
										array(	'host'     => $this->_mailserver,
                                         		'user'     =>  $this->_username,
												'password' => $this->_password,
                                         		'ssl' => true,
												'port' => $this->_port
									));
				
				if(empty($this->_connection)) throw new Exception("Invalid connection");
				$this->_connection_alive = true;
				
		} catch (Exception $e)
		{
			print $e->getMessage();
		}
	}

	private function releaseConnection()
	{
		try {
			$this->_connection->close();
			$this->_connection_alive = false;
		} catch (Exception $e)
		{
			print $e->getMessage();
		}
	}

	public function numberOfMessagesInbox()
	{
		try
		{
			if(!$this->_connection_alive) $this->openConnection();
			return $this->_connection->countMessages();
			$this->releaseConnection();
		} catch (Exception $e)
		{
			print $e->getMessage();
		}
	}

	public function retrieveHeaders()
	{
		try
		{
			if(!$this->_connection_alive) $this->openConnection();
			$headers = array();
			foreach ($message->getHeaders() as $name => $value) 
			{
			    if (is_string($value)) 
			    {
					 $headers[] = sprintf("%s:%s\n", $name, $value);
					 continue;
			    }
			
			    foreach ($value as $entry)
			    	$headers[] = sprintf("%s:%s\n", $name, $entry);
    		}
			$this->releaseConnection();
		} catch (Exception $e)
		{
			print $e->getMessage();
		}
	}

	private function log($msg)
	{
		if(self::LOG)
			Logger::getLogger(__CLASS__)->info($msg);
	}


}
?>