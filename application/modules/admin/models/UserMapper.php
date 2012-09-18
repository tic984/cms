<?php

class Admin_Model_UserMapper
{
    protected $_dbTable;
    
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Admin_Model_DbTable_User');
        }
        return $this->_dbTable;
    }
    
    public function save(Admin_Model_User $user)
    {
        $data = array(
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'salt' => $user->getSalt(),
            'role' => $user->getRole(),
            'date_created' => $user->getDate_created(),
            'gid' => $user->getId(),
        );
 
        if (null === ($id = $user->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Admin_Model_User $user)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $user->setId($row->id)
                  ->setUsername($row->username)
                  ->setPassword($row->password)
                  ->setSalt($row->salt)
                    ->setRole($row->role)
                -> setDate_created($row->date_created)
                ->setGid($row->gid);
    }
 
    public function fetchAll($start=0, $limit=40)
    {
        $resultSet = $this->getDbTable()->fetchAll(null, null, $limit, $start);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Admin_Model_User();
            $entry->setUsername($row->username)
                  ->setPassword($row->password)
                  ->setSalt($row->salt)
                    ->setRole($row->role)
                -> setDate_created($row->date_created)
                ->setGid($row->gid)
                    ->setId($row->id);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function count()
    {
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('count(*) as amount'));
        $rows = $this->getDbTable()->fetchAll($select);
        return($rows[0]->amount);        
    }
 

}

