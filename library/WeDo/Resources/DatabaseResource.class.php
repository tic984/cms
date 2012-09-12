<?php
/**
 * is it what actually handles the connection?
 * if so, has it a descriptor or builds itself on its top and discharge it?
 * @author Alessio
 *
 */
class DatabaseResource
{
	/** receives the descriptor **/
	public function __construct(&$simpleXmlDescriptor)
	{
		parent::fromSimpleXml($simpleXmlDescriptor);
		//now i have in $_descriptor the connection property
		switch($this->getResource()->getProperty('db_type'))
		{
			case 'mysql':
				{
					require_once APPLICATION_PATH.'/code/core/adapters/MysqlAdapter.class.php';
					$this->_adapter =  new MysqlAdapter($resource);
					break;
				}
			case 'mysqli':
				{
					require_once APPLICATION_PATH.'/code/core/adapters/MysqliAdapter.class.php';
					$this->_adapter =  new MysqliAdapter($resource);
					break;
				}
			case 'pdo':
				{
					break;
				}
		}

	}
	public function enroll()
	{
		WeDo_Application::enrollSingleton($this, 'database/default');
	}
}
?>