<?php

class WeDo_Pages_Blocks_Library {

    public $classUri;
    public $contentId;
    public $title;
    public $assetSrc;
    public $fieldName;
    public $blockId;

    public function __construct($classUri, $contentId, $fieldName) {
        $this->classUri = $classUri;
        $this->contentId = $contentId;
        $this->fieldName = $fieldName;
        $this->blockId = $fieldName;
    }

    public function getForm() {
        try {
            $form = new WeDo_Form_Form();
            $form ->addField('classUri', 'hidden', array("value"=> $this->classUri, 'class' => $this->blockId, "id" => ''))
                    ->addField('uploaded', 'uploadify', $this->_getUploadOptions()) //this adds the uploadify behaviour
                    ->addField('fieldName', 'hidden',array("value"=> $this->fieldName, 'class' => $this->blockId, "id" => ''))
                    ->addField('contentId', 'hidden',array("value"=> $this->contentId, 'class' => $this->blockId, "id" => ''));
              
            return $form;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function _getUploadOptions()
    {
        return array('uploadifyOptions' => 
                    array('formData' => 
                        array(
                                "classUri" => $this->classUri, 
                                "contentId"=> $this->contentId,
                                "field" => $this->fieldName,
                            ),
                        ),
                      'options' => array('id' => $this->blockId, 'class' => 'uploadify') 
           );
    }
    
    

}

?>
