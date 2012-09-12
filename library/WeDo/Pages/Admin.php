<?php

class WeDo_Pages_Admin
{

    public $mainBlock;
    public $pageTitle;
    public $otherBlocks;
    public $params;
    
    public function __construct()
    {
        $this->mainBlock = null;
        $this->pageTitle = '';
        $this->otherBlocks = new stdClass();
        $this->params = array();
    }
    
    public function getMainBlock()
    {
        return $this->mainBlock;
    }

    public function setMainBlock($mainBlock)
    {
        $this->mainBlock = $mainBlock;
    }

    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    public function getOtherBlocks()
    {
        return $this->otherBlocks;
    }

    public function setOtherBlocks($otherBlocks)
    {
        $this->otherBlocks = $otherBlocks;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function addBlock($label, $content)
    {
        $this->otherBlocks->$label = $content;
    }

}

?>
