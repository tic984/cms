<?php
require_once APPLICATION_PATH.'code/zend/form_decorators/Dev_Decorator.php';

class Dev_Decorator_CkEditor extends Dev_Decorator
{	
	const JQUERY_CKEDITOR_PATH = 'js/ckeditor/ckeditor.js';
	const JQUERY_CKEDITOR_JQUERY_ADAPTERS_PATH = 'js/ckeditor/adapters/jquery.js';
	
	public function render($content)
	{
		return parent::render($content);
	}
	
	protected function getJSPath($path)
	{
		return sprintf("%s/%s", WeDo_Application::getSingleton('app/environment')->getAdminUrl(), $path);
	}
}

?>