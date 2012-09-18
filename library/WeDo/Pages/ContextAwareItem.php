<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WeDo_Pages_ContextAwareItem
 *
 * @author Ale
 */
class WeDo_Pages_ContextAwareItem {
    
    protected $params;
    protected $baseUri;
    
    public function __construct(Zend_Controller_Request_Abstract &$request) {
        $this->params = $request->getParams(); 
        $this->baseUri = sprintf("/%s/%s/%s", $this->params['module'], $this->params['controller'], $this->params['action']);
    }
}

?>
