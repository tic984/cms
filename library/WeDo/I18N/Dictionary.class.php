<?php
class Dictionary
{
	const MODULE_DIC_PATH = "/dic/";

	const GLOBAL_DIC_PATH = "code/core/I18N/dic/";

	public function __construct($module)
	{
		if($module == 'application')
		$path_to_dic = APPLICATION_PATH.self::GLOBAL_DIC_PATH;
		else
		{
			$codepool = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->detectCodePool($module);
			$path_to_dic = ($codepool == 'local') ? '' : '';
		}
		if(is_file($path_to_dic))
		{
			$this->dic = parse_ini_file($path_to_dic);
			if($this->dic===false)
			return false;
			return true;
		}
		return false;
	}
}
?>