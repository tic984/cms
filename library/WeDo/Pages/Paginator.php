<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paginator
 *
 * @author Ale
 */
class WeDo_Pages_Paginator {
    
    public $curPage;
    public $itemsPerPage;
    public $itemsCount;
    public $numPages;
    public $hasPages;
    public $start;
    public $params;
    public $baseUri;
    
    
    const REQUEST_PARAM_PAG = 'pag';
    const REQUEST_PARAM_ITEMS_PER_PAGE = 'ipp';
    
    const DEFAULT_ITEMS_PER_PAGE = 15;
    
    public function __construct(Zend_Controller_Request_Abstract &$request) {
       $this->curPage = $request->getParam(self::REQUEST_PARAM_PAG, 1);
       $this->itemsPerPage = $request->getParam(self::REQUEST_PARAM_ITEMS_PER_PAGE, self::DEFAULT_ITEMS_PER_PAGE);
       $this->params = $request->getParams(); 
       $this->baseUri = sprintf("/%s/%s/%s", $this->params['module'], $this->params['controller'], $this->params['action']);
       $this->itemsCount = 0;
       $this->numPages = 1;
       $this->hasPages = false;
       $this->start = 0;
    }
    
    public function getCurPage() {
        return $this->curPage;
    }

    public function setCurPage($curPage) {
        $this->curPage = $curPage;
        return $this;
    }

    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

    public function getItemsCount() {
        return $this->itemsCount;
    }

    public function setItemsCount($itemsCount) {
        $this->itemsCount = $itemsCount;
        return $this;
    }

    public function hasPages() {
        return $this->hasPages;
    }

    public function setHasPages($hasPages) {
        $this->hasPages = $hasPages;
        return $this;
    }

    public function getNumPages()
    {
        return $this->numPages; 
    }
    
    public function getStart()
    {
        return $this->start;
    }
    public function prepare()
    {
        $this->hasPages = ($this->getItemsCount () > $this->getItemsPerPage ());
        $this->numPages = ceil($this->itemsCount / $this->itemsPerPage);
        $this->start = ($this->getCurpage() - 1) * $this->getItemsPerPage();
        return $this;      
    }
    
    public function getLink($pos)
    {
        $link = array($this->baseUri);
        
        foreach($this->params as $k => $v)
        {
            switch($k)
            {
                case 'module':
                case 'controller':
                case 'action':
                    continue;
                    break;
                case self::REQUEST_PARAM_PAG:
                    $link[] = sprintf("%s/%s", self::REQUEST_PARAM_PAG, $pos);
                    break;
                default:
                    $link[] = sprintf("%s/%s", $k, $v);
                    break;
            }
        }   
        return implode("/", $link);
    }
    
    public function previous()
    {
        return $this->getLink($this->getCurPage() -1 );
    }
    
    public function next()
    {
        return $this->getLink($this->getCurPage() +1 );
    }
}

?>
