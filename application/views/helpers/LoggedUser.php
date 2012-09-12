<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LoggedUser
 *
 * @author Alessio
 */
class Zend_View_Helper_LoggedUser extends Zend_View_Helper_Abstract 
{

    public function loggedUser ()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            $username = $auth->getIdentity()->username;
            $logoutUrl = $this->view->url(array('controller' => 'login',
                'action' => 'logout'), null, true);
            return $content = sprintf('Logged in as: <a href="#">%s </a> | <a href="%s">Logout</a>', $username, $logoutUrl);
        }
    }

}

?>
