<?php
class WeDo_Plugins_Auth_Check extends Zend_Controller_Plugin_Abstract
{

    private $_controller;
    private $_module;
    private $_action;
    private $_role;

    //gets involved evrt we make a request
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_controller = $request->getControllerName();
        $this->_module = $request->getModuleName();
        $this->_action = $request->getActionName();
        //$this->getResponse()->appendBody("<!-- ocio:".$this->_controller.",".$this->_module.",".$this->_action." -->");


        switch ($this->_module)
        {
            case 'login':
            case 'default':
                if ($this->_controller == 'error')
                {
//                    $this->_request->setModuleName('default');
//                    $this->_request->setControllerName('index');
//                    $this->_request->setActionName('index');
                }
                break;
            default:
                $this->_checkIdentity();
                break;
        }
    }

    private function _checkIdentity()
    {
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
           $this->_request->setModuleName('default');
           $this->_request->setControllerName('login');
           $this->_request->setActionName('index');
        }
    }

}
?>
