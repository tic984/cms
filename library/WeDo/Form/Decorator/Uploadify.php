<?php

class WeDo_Form_Decorator_Uploadify extends Zend_Form_Decorator_File
{
    
    protected function buildLabel()
    {
        $element = $this->getElement();
        $label = $element->getLabel();
        if (trim($label) != '')
        {
            if ($translator = $element->getTranslator())
            {
                $label = $translator->translate($label);
            }
            if ($element->isRequired())
            {
                $label .= '*';
            }
            $label .= ':';
            return $element->getView()
                            ->formLabel($element->getName(), $label);
        }
    }

    
    public function buildInput()
    {
        $element = $this->getElement();
        
        $helper = $element->helper;
        $file = $element->getView()->$helper($element->getName(), $element->getAttribs());
        return $file;
    }
    protected function buildErrors()
    {  
        if (!$this->getElement()->hasErrors())
        {
            return '';
        }
        else 
        {
            $first_error = current($this->getElement()->getErrors());
            return sprintf('<span class="error-message">%s</span>', trim($first_error));
        }
    }

   protected function buildDescription()
    {
        $element = $this->getElement();
        $desc = $element->getDescription();
        if (empty($desc))
        {
            return '';
        }
        return '<div class="description">' . $desc . '</div>';
    }

    
   public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element)
            return $content;
        
        $view = $element->getView();
        if (!$view instanceof Zend_View_Interface)
            return $content;        

        $name = $element->getName();
        
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label = $this->buildLabel();
        $input = $this->buildInput();
        $errors = $this->buildErrors();
        $desc = $this->buildDescription();
        $markup = array();
        $size = $element->getMaxFileSize();
        $extra_class = $this->getOption('class');
        if(is_array($extra_class))
            $itemClass = empty($extra_class) ? 'input' : sprintf("input %s", implode(" ", $extra_class));
        else 
             $itemClass = empty($extra_class) ? 'input' : sprintf("input %s", $extra_class);
        if($this->getElement()->hasErrors())
            $itemClass .= ' error';


        $outputtpl = <<<OUTPUT
            			
                <div class="%s">
             		%s
                        %s
                    	%s
                </div>
OUTPUT
        ;
        
        $output = sprintf($outputtpl,$itemClass, $label, $input, $errors);
        

        switch ($placement)
        {
            default:
            case self::PREPEND:
                return $output . $separator . $content;
            case self::APPEND:
                return $content . $separator . $output;
        }
    } 

}

?>