<?php

class WeDo_Form_Decorator_AjaxFile extends Zend_Form_Decorator_File
{
    
    protected function buildLabel()
    {
       
    }


    public function buildInput()
    {
        
    }

    protected function buildErrors()
    {  
        
    }

   protected function buildDescription()
    {
        
    }

    
   public function render($content)
    {
        return '<div id="csv">      
    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript>         
</div>
';
    } 

}

?>