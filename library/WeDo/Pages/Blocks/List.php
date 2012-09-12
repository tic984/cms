<?php

class WeDo_Pages_Blocks_List
{

    public $title;
    public $rowTitles;
    public $rows;
    public $itemsPerPage;
    public $itemsCount;
    public $curPage;
    public $paginationMax;
    public $links;

    
    public function __construct($linkToken)
    {
        $this->links = new stdClass();
        $this->links->list = '/admin/'.$linkToken.'/index/pag/%d';
        $this->links->action =  '/admin/contents/%s/'.$linkToken;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getRowTitles()
    {
        return $this->rowTitles;
    }

    public function setRowTitles($rowTitles)
    {
        $this->rowTitles = $rowTitles;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    public function setItemsCount($itemsCount)
    {
        $this->itemsCount = $itemsCount;
    }

    public function getCurPage()
    {
        return $this->curPage;
    }

    public function setCurPage($curPage)
    {
        $this->curPage = $curPage;
    }

    public function getPaginationMax()
    {
        return $this->paginationMax;
    }

    public function setPaginationMax($paginationMax)
    {
        $this->paginationMax = $paginationMax;
    }

    public function show()
    {
        if ($this->itemsPerPage > $this->itemsCount)
            return false;
        return true;
    }

    public function max()
    {
        if ($this->paginationMax == '')
            $this->paginationMax = ceil($this->itemsCount / $this->itemsPerPage);
        return $this->paginationMax;
    }

    public function showPrev()
    {
        return ($this->curPage > 1);
    }

    public function showNext()
    {
        return ($this->curPage < $this->paginationMax);
    }

    public function linkPage($pageNum)
    {
        return sprintf($this->links->list, $pageNum);
    }

    public function linkPrev()
    {
        return sprintf($this->links->list, $this->curPage - 1);
    }

    public function linkNext()
    {
        return sprintf($this->links->list, $this->curPage + 1);
    }
    
    public function linkAction($action, $params)
    {
        $baselink = sprintf($this->links->action, $action);
        foreach($params as $k => $v)
            $baselink .= sprintf("/%s/%s", $k, $v);
        return $baselink;
    }
    
    public function getSkip()
    {
        return ($this->curPage -1) * $this->itemsPerPage;
    }

}

?>
