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
    
    public $curpage;
    public $itemsPerPage;
    public $itemsCount;
    public $numPages;
    public $hasPages;
    public $start;
    
    public function __construct(Zend_Controller_Request_Abstract &$request) {
       $this->curPage = $request->getParam('pag', 1);
       $this->itemsPerPage = 30;
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

}

?>
