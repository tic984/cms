<?php

class WeDo_Pages_Blocks_Form
{

    public $title;
    public $form;

    
    public function __construct()
    {
        
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }
    public function getForm()
    {
        return $this->form;
    }
    

}

?>
