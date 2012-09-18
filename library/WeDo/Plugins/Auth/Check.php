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

        switch ($this->_module)
        {
            case 'login':
            case 'default':
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
           $this->_request->setModuleName('admin');
           $this->_request->setControllerName('login');
           $this->_request->setActionName('index');
        }
    }

}
?>
