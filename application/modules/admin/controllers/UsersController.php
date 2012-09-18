<?php

class Admin_UsersController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function listAction()
    {
        try {
            $mapper = new Admin_Model_UserMapper();
            $this->view->currenPageName = 'Utenti ';
            $this->view->paginator = new WeDo_Pages_Paginator($this->getRequest());
            $this->view->actions = new WeDo_Pages_ListActions($this->getRequest());
            $this->view->actions
                    ->addAction('disable', "Disattiva")
                    ->addAction('enable', "Attiva");
            $this->view->paginator 
                    ->setItemsCount($mapper->count())
                    ->prepare();
            $this->view->entries = $mapper->fetchAll($this->view->paginator->getStart(), $this->view->paginator->getItemsPerPage());
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    public function newAction()
    {
        try {

            $request = $this->getRequest();
            $form = $this->getForm();
            $form->setAction('/user/new');

            if ($this->getRequest()->isPost())
            {
                if ($form->isValid($request->getPost()))
                {
                    $user = new Application_Model_User($form->getValues());
                    $user->save();
                    $this->setNotif('success', 'Success', 'Operazione completata con successo');
                } else
                    $this->setNotif('warning', 'Attenzione', 'Non sono stati inseriti tutti i campi richiesti');
            }

            $this->view->form = $form;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function editAction()
    {
        try {

            $request = $this->getRequest();
            $form = $this->getForm();
            $form->setAction('/user/edit');

            if ($this->getRequest()->isPost())
            {

                if ($form->isValid($request->getPost()))
                {
                    $user = new Application_Model_UserMapper();
                    $user->find($this->getRequest()->getParam('_id'))->importMap($form->getValues())->save();
                    $this->setNotif('success', 'Success', 'Operazione completata con successo');
                } else
                    $this->setNotif('warning', 'Attenzione', 'Non sono stati inseriti tutti i campi richiesti');
            }
            else
            {
                $id = $this->getRequest()->getParam('id');
                $mapper = new Application_Model_UserMapper();
                $object = $mapper->find($id);
                $form->populate($object->getMap());
            }

            $this->view->form = $form;
        } catch (Exception $exc) {
            throw $exc;
        }
    }


}



