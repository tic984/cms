<?php

class WeDo_Form_XUiWidgetDecorator extends ZendX_JQuery_Form_Decorator_UiWidgetElement
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

    protected function buildInput()
    {
        $element = $this->getElement();
        $helper = $element->helper;
        return $element->getView()->$helper(
                        $element->getName(), $element->getValue(), $element->getAttribs(), $element->options
        );
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
            return sprintf('<span class="error-message">%s</span>', $first_error);
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
        {
            return $content;
        }
        if (null === $element->getView())
        {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label = $this->buildLabel();
        $input = $this->buildInput();
        $errors = $this->buildErrors();
       
        $desc = $this->buildDescription();
        $extra_class = $this->getOption('class');
        if(is_array($extra_class))
            $itemClass = empty($extra_class) ? 'input' : sprintf("input %s", implode(" ", $extra_class));
        else 
             $itemClass = empty($extra_class) ? 'input' : sprintf("input %s", $extra_class);
        
        $outputtpl = <<<OUTPUT
                <div class="%s">
             		%s
                        %s
                    	%s
                </div>
OUTPUT
        ;

        $output = sprintf($outputtpl, $itemClass, $label, $input, $errors);

        switch ($placement)
        {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }

}

?>