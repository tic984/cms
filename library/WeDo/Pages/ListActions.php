<?php

/**
 * Description of ListActions
 *
 * @author Ale
 */
class WeDo_Pages_ListActions extends WeDo_Pages_ContextAwareItem {
    
    public $list;
    
    public function __construct(Zend_Controller_Request_Abstract &$request, $list = array()) {
        parent::__construct($request);
        if(empty($list))
            $list = array("delete" => "Rimuovi");
        $this->list = $list;
    }
    
    public function addAction($action, $label)
    {
        $this->list[$action] = $label; 
        return $this;
    }
}

?>
