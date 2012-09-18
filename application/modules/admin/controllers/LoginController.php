<?php

class Admin_LoginController extends Zend_Controller_Action
{

    public function getForm()
    {
        return new Admin_Form_Login(array(
                    'action' => '/admin/login',
                    'method' => 'post',
                ));
    }


    protected function _getAuthAdapter()
    {
        $dbAdapter = WeDo_Application::getSingleton('database/default')->toZendDbAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('users')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('SHA1(CONCAT(?,salt))');


        return $authAdapter;
    }

    public function init()
    {
        $this->_helper->layout = Zend_Layout::getMvcInstance();
        $this->_helper->layout->setLayout('login');
    }

    public function indexAction()
    {
        $form = $this->getForm();
        $request = $this->getRequest();
        if ($request->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                if ($this->_process($form->getValues()))
                    $this->_redirect('/admin/stats');
            }
        }
        $this->view->form = $form;
    }

    protected function _process($values)
    {
        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values['username']);
        $adapter->setCredential($values['password']);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid())
        {
            $user = $adapter->getResultRowObject();
            $auth->getStorage()->write($user);
            return true;
        }
        return false;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('/admin'); // back to login page
    }


}

