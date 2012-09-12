<?php
define("ROOT_PATH", realpath(".").DIRECTORY_SEPARATOR);
define("APPLICATION_PATH", dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR);
define("ADMIN_PATH", realpath('.').DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR);

require_once(APPLICATION_PATH."Application.class.php");
require_once(APPLICATION_PATH."WeDo_Helpers_Application.class.php");
require_once(APPLICATION_PATH."code/Descriptor.class.php");
require_once(APPLICATION_PATH."code/ApplicationDescriptor.class.php");
require_once(APPLICATION_PATH."code/XmlHelper.class.php");
require_once(APPLICATION_PATH."code/Environment.class.php");
require_once(APPLICATION_PATH."code/core/adapters/AdapterFactory.class.php");
require_once(APPLICATION_PATH."code/RunLevel.class.php");
require_once(APPLICATION_PATH."code/Connection.class.php");
require_once(APPLICATION_PATH."code/DatabaseConnection.class.php");
require_once(APPLICATION_PATH."code/EmailConnection.class.php");
require_once(APPLICATION_PATH."code/ModuleManager.class.php");
require_once(APPLICATION_PATH."codex.php");

require_once(APPLICATION_PATH."lib/zend/library/Zend/Loader.php");
//WeDo_Application::runFor('frontend');

?>