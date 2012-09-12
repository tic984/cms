<?php

class WeDo_Form_Element_Uploadify extends Zend_Form_Element_File
{

    private $_uploadifyOptions;

    public function __construct($spec, $uploadifyOptions=array(), $options = null)
    {
        parent::__construct($spec, $options);
        $this->_uploadifyOptions = $this->_initOptions($uploadifyOptions);
        $this->setAttrib('id', $this->_myId());
        $this->setDecorators(array(new WeDo_Form_Decorator_Uploadify()));
        
        $this->getView()->headLink()->appendStylesheet('/js/admin/uploadify/uploadify.css');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/uploadify/jquery.uploadify.v2.1.4.min.js');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/uploadify/swfobject.js');
        $this->getView()->jQuery()->addOnload($this->_getOnloadCode());   
    }
    
    private function _myId()
    {
        return sprintf("%s_%s", "file", rand());
    }
    
//    private function _myId()
//    {
//        return sprintf("%s_%s", "file", $this->getName());
//    }

    private function _initOptions($uploadifyOptions)
    {
        $options_array = array(
            'auto' => 'true',
            'buttonImg' => '',
            'buttonText' => '',
            'cancelImg' => "'/js/admin/uploadify/cancel.png'",
            'checkScript' => '',
            'displayData' => '',
            'expressInstall' => '',
            'fileDataName' => "'" . $this->getName() . "'",
            'fileDesc' => '',
            'fileExt' => '',
            'folder' => "'/uploads'",
            'height' => '',
            'hideButton' => '',
            'method' => '',
            'multi' => 'false',
            'queueID' => '',
            'queueSizeLimit' => '',
            'removeCompleted' => '',
            'rollover' => '',
            'script' => "'/fupload/uploadify'",
            'scriptAccess' => "'always'",
            'scriptData' => array("fileDataName" => $this->getName()),
            'simUploadLimit' => '',
            'sizeLimit' => '',
            'uploader' => "'/admin/content/uploader/upload'",
            'width' => '',
            'wmode' => '',
            'onAllComplete' => '',
            'onCancel' => '',
            'onCheck' => '',
            'onClearQueue' => '',
            'onComplete' => $this->_getOnComplete(),
            'onError' => '',
            'onInit' => '',
            'onOpen' => '',
            'onProgress' => '',
            'onQueueFull' => '',
            'onSelect' => '',
            'onSelectOnce' => '',
            'onSWFReady' => ''
        );


        foreach ($options_array as $k => $v)
        {

            switch ($k)
            {
                case 'scriptData':
                    if (isset($uploadifyOptions[$k]))
                    {
                        foreach ($uploadifyOptions[$k] as $param => $value)
                            $options_array['scriptData'][$param] = $value;
                    }
                    break;
                case 'onAllComplete':
                case 'onAllComplete':
                case 'onCancel':
                case 'onCheck':
                case 'onClearQueue':
                case 'onComplete':
                case 'onError':
                case 'onInit':
                case 'onOpen':
                case 'onProgress':
                case 'onQueueFull':
                case 'onSelect':
                case 'onSelectOnce':
                case 'onSWFReady':
                    if (isset($uploadifyOptions[$k]))
                        $v = $uploadifyOptions[$k];
                    $options_array[$k] = $this->_getHandlerCode($v);
                    break;
                default:
                    $options_array[$k] = $v;
                    break;
            }
        }

        //jsonencode scriptdatas
        $options_array['scriptData'] = json_encode($options_array['scriptData']);
        return $options_array;
    }

    private function _getOnloadCode()
    {

        return sprintf("
                $('#%s').uploadify(
                {
   %s
                    
                });", $this->getAttrib('id'), $this->_getJsParams()
        );
    }

    private function _getHandlerCode($code)
    {
        $tpl = <<<TPL
function(event, ID, fileObj, response, data) 
\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\t%s
\t\t\t\t\t\t\t\t\t}
TPL;
        if($code == '') return '';
        return sprintf($tpl, $code);
    }

    private function _getJsParams()
    {
        $js = array();
        foreach ($this->_uploadifyOptions as $k => $v)
        {
            if (empty($v))
                continue;
            $js[] = sprintf("\t\t\t\t\t\t\t%s:%s", $k, $v);
        }
        return implode(",\n", $js);
    }
    
    private function _getOnComplete()
    {
         return sprintf(
                 "
                     $(\"#%s\").val(response);
                     
                 ",
                 $this->getName()
                 );
    }

}

?>
