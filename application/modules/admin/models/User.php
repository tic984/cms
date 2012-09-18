<?php

class Admin_Model_User
{
    protected $_id;
    protected $_username;
    protected $_password;
    protected $_salt;
    protected $_role;
    protected $_date_created;
    protected $_gid;
    
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        return $this->$method();
    }
 
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
        return $this;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }

    public function getSalt() {
        return $this->_salt;
    }

    public function setSalt($salt) {
        $this->_salt = $salt;
        return $this;
    }

    public function getRole() {
        return $this->_role;
    }

    public function setRole($role) {
        $this->_role = $role;
        return $this;
    }

    public function getDate_created() {
        return $this->_date_created;
    }

    public function setDate_created($date_created) {
        $this->_date_created = $date_created;
        return $this;
    }

    public function getGid() {
        return $this->_gid;
    }

    public function setGid($gid) {
        $this->_gid = $gid;
        return $this;
    }



}

