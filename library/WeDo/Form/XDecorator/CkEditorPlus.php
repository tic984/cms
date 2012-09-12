<?php
require_once APPLICATION_PATH.'code/zend/form_decorators/CkEditor.php';

class Dev_Decorator_CkEditorPlus extends Dev_Decorator_CkEditor
{
	
	public function render($content)
	{
		$element = $this->getElement();
		$element_name = $element->getLabel();
		$html_view = $element->getView();
		
		$html_view->jQuery()->addJavascriptFile($this->getJSPath(self::JQUERY_CKEDITOR_PATH));
		$html_view->jQuery()->addJavascriptFile($this->getJSPath(self::JQUERY_CKEDITOR_JQUERY_ADAPTERS_PATH));
		$html_view->jQuery()->addOnload(sprintf("$('#%s').ckeditor({toolbar: 'Basic'});", $element->getName()));
		return parent::render($content);
	}

}

?>