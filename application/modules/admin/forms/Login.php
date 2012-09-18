<?php

class Admin_Form_Login extends Zend_Form
{

    public function init()
    {
         // Set the method for the display form to POST
        $this->setMethod('post');
        
        $username = new WeDo_Form_Element_Text('username');
        $username->setLabel('Username')
                ->setRequired(true)
                ->setFilters(array('StringTrim', 'StringToLower'))
                ->setValidators(array('Alpha',array('StringLength', false, array(3, 20))));
        
       $password = new WeDo_Form_Element_Password('password');
       $password->setLabel('Password')
                ->setRequired(true)
                ->setFilters(array('StringTrim'))
                ->setValidators(array('Alnum',array('StringLength', false, array(6, 20))));
        
        $this->addElement($username)
             ->addElement($password);
            
    }


}

