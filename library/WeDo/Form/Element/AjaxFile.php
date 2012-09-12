<?php

class WeDo_Form_Element_AjaxFile extends Zend_Form_Element_File
{

    public static $instance = 1;
    private $_options;

    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->_options = $options;
        $this->setDecorators(array(new WeDo_Form_Decorator_AjaxFile()));
        $this->getView()->headLink()->appendStylesheet('/js/valums-uploader/fileuploader.css');
        $this->getView()->headScript()
                ->appendFile('/js/valums-uploader/fileuploader.js')
                ->appendScript($this->_getJs());
        self::$instance +=1;
    }

    private function _getJS()
    {
        $content = <<<TPL
            function %sIsUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('%s')%s                
            });           
        }
        
        window.onload = %sIsUploader;
TPL;
        $params = array();
        foreach($this->_options as $k => $v)
        {
            if(substr($k, 0, 6) == 'param_')
            {
                $k = substr($k, 6);
                $params[] = sprintf("\t\t\t%s: %s", $k, $v);
            }
        }
        $extra_params = empty($params) ? '': ",\n\t".implode(",\n\t", $params);
        return sprintf($content, $this->getName(), $this->getName(),  $extra_params, $this->getName());
    }

}

?>
