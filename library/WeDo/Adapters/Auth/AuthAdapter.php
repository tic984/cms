<?php

class AuthAdapter implements Zend_Auth_Adapter_Interface
{

    private $_identity = '';
    private $_credentials = '';

    public function __construct()
    {
        
    }

    public function setIdentity($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

    public function setCredentials($password)
    {
        $this->_credentials = $password;
        return $this;
    }

    public function authenticate()
    {
        try {
            $salt = $this->getSalt();

            if ($salt === false)
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity);

            $checkUserQuery = new WeDo_Db_Query_Select();
            $checkUserQuery->select(array('id', 'status', 'username', 'email'))
                    ->from("tbl_users")
                    ->where(array("username = '?'" => $this->_identity))
                    ->where(array("password = '?'" => sha1($this->_credentials . $salt)))
                    ->limit(0, 1);
            $result = WeDo_Application::getSingleton('database/default')->fetchObject($checkUserQuery->getQuery());

            if (empty($result))
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity);
            switch ($result->status)
            {
                case Adminusers_Model_User::STATUS_DISABLED:
                    return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_identity);
                case Adminusers_Model_User::STATUS_RENEW:
                    return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, $this->_identity);
                case Adminusers_Model_User::STATUS_ACTIVE:
                    return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $result);
            }
        } catch (Exception $e) {
            throw new Zend_Auth_Adapter_Exception;
        }
    }

    private function getSalt()
    {
        try {
            $getSaltQuery = new WeDo_Db_Query_Select();
            $getSaltQuery->select("salt")
                    ->from("tbl_users")
                    ->where(array("username = '?'" => $this->_identity))
                    ->limit(0, 1);
            $salt = WeDo_Application::getSingleton('database/default')->fetchResult($getSaltQuery->getQuery());
            if (empty($salt))
                return false;
            return $salt;
        } catch (Exception $e) {
            throw $e;
        }
    }

}

?>