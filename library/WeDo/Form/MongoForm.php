<?php

class WeDo_Form_MongoForm extends WeDo_Form_Form {

    public function __construct($options = null) {
        parent::__construct($options);
    }

    public function addIdField($val = -1) {
        if($val instanceof MongoId)
            $val = $val->{'$id'};
        $element = new WeDo_Form_Element_Hidden('id');
        $element->setRequired(true)
                ->setValue($val);
        $this->addElement($element);
        return $this;
    }

}

?>
