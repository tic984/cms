<?php

class WeDo_Form_Decorator_XDecorator extends ZendX_JQuery_Form_Decorator_UiWidgetElement
{

    protected function buildLabel()
    {
        $element = $this->getElement();
        $label = $element->getLabel();
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
        $element = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages))
        {
            return '';
        }
        return '<div class="errors"><p>' . $element->getView()->formErrors($messages) . '</p></div>';
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

        $outputtpl = <<<OUTPUT
            			
             		<div class="formBlock">
             					%s
                    		<div class="content">
                    			%s
                    		</div>
                    			%s
                   		 	<div class='clearer'></div>
                     </div>
OUTPUT
        ;

        $output = sprintf($outputtpl, $label, $input, $errors);

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