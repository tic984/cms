<?php
class EmailAdapterFactory
{
	/**
	 * returns adapter based on 'adapter' attribute
	 * @param unknown_type $simplexmlDescriptor
	 */
	public static function getAdapter(&$simplexmlDescriptor)
	{
		try
		{
			$adapterType = $simplexmlDescriptor['adapter'];
			$security = $simplexmlDescriptor->ssl;
			$host = $simplexmlDescriptor->host;
			$port = $simplexmlDescriptor->port;
			$login = $simplexmlDescriptor->login;
			$password = $simplexmlDescriptor->password;
				
			$conn_name = $simplexmlDescriptor['connection_name'];
				
			switch($adapterType)
			{
				case 'imap':
					require_once APPLICATION_PATH."code/core/adapters/ImapAdapter.class.php";
					return new ImapAdapter($conn_name, $host, $port, $login, $password, $security);
					break;
				default:
					throw new Exception("Adapter not found");
			}
		} catch (Exception $e)
		{
			throw $e;
		}
	}
}
?>