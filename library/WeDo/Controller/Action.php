<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Action
 *
 * @author Alessio
 */
class WeDo_Controller_Action extends Zend_Controller_Action {

    protected $_mapper;
    protected $_classuri;
    protected $_className;
    
    
    protected function _indexBlock() {

        $listBlock = new WeDo_Pages_Blocks_List($this->_classuri);
        $listBlock->curPage = $this->_getParam('pag', 1);
        $listBlock->itemsPerPage = $this->_getParam('ipp', 20);

        $skip = $listBlock->getSkip();

        $listBlock->rows = $this->_mapper->all(array(), array(), $skip, $listBlock->itemsPerPage);
        $listBlock->itemsCount = $this->_mapper->count();

        $listBlock->rowTitles = array("Nome", "Email", "Descrizione");
        return $listBlock;
    }

    protected function _save() {
        try {
            $id = $this->_getParam('_id');
            if (!empty($id))
                $content = call_user_func(array($this->_className, 'find'), $id);
            else
            {
                $cname = $this->_className;
                $content = new $cname();
            }
            $content->fromRequest()->save();
            $id = $content->getId();
            if (empty($id))
                throw new Exception("Item not Saved");
            return $id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function setNotif($type, $title, $message) {
        $notif = new stdClass();
        $notif->type = $type;
        $notif->title = $title;
        $notif->message = $message;
        $this->view->notif = $notif;
    }

    protected function _fromLibrary($field, &$content) {
        try {
            $asset = $content->$field;
            $block = new WeDo_Pages_Blocks_Library($this->_className, $content->getId(), $field);
            $block->assetSrc = WeDo_Libs_TimThumb::getLink("/assets/$asset", 300);
            $block->title = sprintf("Campo <i>'%s'</i>", $field);
            $block->form = $block->getForm();
            return $block;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function _editBlock($content) {
        try {
            if(!is_object($content)) throw new Exception("content is not an object, content is". var_dump ($content));
            if ($content instanceof WeDo_Form || $content instanceof Zend_Form || $content instanceof WeDo_Form_Ajax)
                $form = $content;
            else
                $form = $this->_form($content->toMap());
            return $this->_formBlock($form);
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function _newBlock() {

        return $this->_formBlock();
    }

    protected function _formBlock($form = null) {

        $block = new WeDo_Pages_Blocks_Form();
        $block->form = (empty($form)) ? $this->_form() : $form;
        return $block;
    }

    protected function _form($content = null) {
        $formClass = $this->_formClass;
        $form = new $formClass();
        $form->setAction("/admin/".$this->_classuri."/save");
        if (empty($content))
            return $form;
        $form->populate($content);
        return $form;
    }
    
    
}

?>
