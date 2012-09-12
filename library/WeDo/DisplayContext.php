<?php

/**
 * Groups base display params, such as order by, order obj, start, len
 *
 * @author alessio
 *
 */
class WeDo_DisplayContext
{

    private $_orderBy;
    private $_start;
    private $_len;
    private $_classUri;
    private $_orderMethod;
    private $_active;
    private $_deleted;
    private $_owner;
    private $_view;

    const ORDERBY_AS_FOLLOW = 1;
    const USE_CLASS_DEFINED_SORTING = 2;
    const ORDER_BY_NONE = 3;

    public static function fromRequest($classUri, $action = 'index', $view = 'adminlist')
    {
        $start = false;
        $len = false;

        $displayContext = self::initByAction($classUri, $action);

//		if(Request::getRequest()->isRequestGet())
//		{
//			$start = Request::getRequest()->getParamFromGet('start', FILTER_SANITIZE_NUMBER_INT);
//			$len = Request::getRequest()->getParamFromGet('len', FILTER_SANITIZE_NUMBER_INT);
//				
//		}
//		if(Request::getRequest()->isRequestPost())
//		{
//			$start = Request::getRequest()->getParamFromPost('start', FILTER_SANITIZE_NUMBER_INT);
//			$len = Request::getRequest()->getParamFromPost('len', FILTER_SANITIZE_NUMBER_INT);
//		}



        if ($start !== false)
            $displayContext->setStart($start);
        if ($len !== false)
            $displayContext->setLen($len);
        $displayContext->setView($view);

        return $displayContext;
    }

    private static function initByAction(&$classUri, $action)
    {
        
        $descriptor = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getModuleDescriptor($classUri->_prefix);
        $sorting_name = $descriptor->getClassBackendPageProperty($classUri->_className, $action, 'sorting');

        $orderBy = '';
        $orderMethod = '';

        if (empty($sorting_name))
        {
            if ($descriptor->classHasDefaultSortingOptions($classUri->_className))
            {
                $orderMethod = self::USE_CLASS_DEFINED_SORTING;
                $orderBy = $descriptor->getClassSortingOptions($classUri->_className, 'default');
            } else
                $orderMethod = self::ORDER_BY_NONE;
        }
        else
        {
            $orderMethod = self::ORDERBY_AS_FOLLOW;
            $orderBy = $descriptor->getClassSortingOptions($classUri->_className, $sorting_name);
        }

        $d = new WeDo_DisplayContext($classUri, $orderMethod);

        if ($orderBy != '')
            $d->setOrderBy($orderBy);

        $len = $descriptor->getClassBackendPageProperty($classUri->_className, $action, 'itemsInList');
        $d->setLen($len);
        return $d;
    }

    public function __construct($classUri, $orderMethod=self::USE_CLASS_DEFINED_SORTING)
    {
        $this->_classUri = $classUri;
        $this->_orderBy = array();
        $this->_orderMethod = $orderMethod;
        $this->_start = 0;
        $this->_len = 0;
        $this->_active = '';
        $this->_deleted = 'N';
        $this->_owner = '';
        $this->_view = '';
    }

    public function getStart()
    {
        return $this->_start;
    }

    public function getLen()
    {
        return $this->_len;
    }

    public function setStart($s)
    {
        $this->_start = $s;
        return $this;
    }

    public function setLen($s)
    {
        $this->_len = $s;
        return $this;
    }

    public function setOrderBy($fields)
    {
        if ($this->_orderMethod == self::ORDERBY_AS_FOLLOW)
        {
            if (is_array($fields))
            {
                foreach ($fields as $f)
                    $this->_orderBy[] = $f;
            } else
                $this->_orderBy[] = $fields;
        }
        return $this;
    }

    public function applyClassOrderBy()
    {
       
        $moduleDescriptor = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getModuleDescriptor($classUri->_prefix);
        if ($moduleDescriptor->classHasDefaultSortingOptions($classUri->_className))
        {
            foreach ($moduleDescriptor->getClassDefaultSortingOptions($classUri->_className) as $classSortingMethod)
                $this->_orderBy[] = $classSortingMethod;
        }
    }

    public function useOrderBy()
    {
        if (count($this->_orderBy) == 0)
            return false;
        return true;
    }

    public function getOrderBy()
    {
        return $this->_orderBy;
    }

    public function getOrderByForQuery()
    {
        print_r($this->_orderBy);
    }

    public function useLimit()
    {
        if ($this->_len == 0)
            return false;
        return true;
    }

    public function useActive()
    {
        if ($this->_active == '')
            return false;
        return true;
    }

    public function useDelete()
    {
        if ($this->_deleted == '')
            return false;
        return true;
    }

    public function useOwner()
    {
        if ($this->_owner == '')
            return false;
        return true;
    }

    public function getActive()
    {
        return $this->_active;
    }

    public function getDeleted()
    {
        return $this->_deleted;
    }

    public function getOwner()
    {
        return $this->_owner;
    }

    public function getView()
    {
        return $this->_view;
    }

    public function setView($view)
    {
        $this->_view = $view;
        return $this;
    }

    public function useView()
    {
        if ($this->_view == '')
            return false;
        return true;
    }

}

?>