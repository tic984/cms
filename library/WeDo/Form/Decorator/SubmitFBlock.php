<?php

class WeDo_Form_Decorator_SubmitFBlock extends WeDo_Form_Decorator
{

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
        $input = $this->buildInput();


        $outputtpl = <<<OUTPUT
            			
             <div class="submit">
                %s
             </div>
OUTPUT
        ;

        $output = sprintf($outputtpl, $input);
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