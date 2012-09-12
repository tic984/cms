<?php

class Devel_Controller_Action_Helper extends Zend_Controller_Action_Helper_Abstract
{

    protected $_scripts_path;
    protected $_view;
    protected $_classUri;
    protected $_className;
    protected $_moduleName;
    protected $_controllerName;
    protected $_relationHelper;

    public function init()
    {
        try {
            parent::init();

            if (get_class($this->_actionController) != "ErrorController")
            {

                $moduledescriptor = $this->_actionController->getModuleDescriptor();
                if (empty($moduledescriptor))
                {
                    throw new Exception("It is impossible to obtain moduleDescriptor from " . get_class($this->_actionController));
                    die();
                }
                if ($moduledescriptor->classHasRelations($this->_actionController->getClassName()))
                    $this->_relationHelper = new Devel_Action_Helper_Relations($this->_actionController->getClassUri());

                $this->_classUri = $this->_actionController->getClassUri();
                list($moduleName, $className) = WeDo_Helpers_Application::explodeClassUri($this->_classUri);
                $this->_className = $className;
                $this->_moduleName = $moduleName;
               
            }
        } catch (Exception $e) {
            print $e->getMessage();
            // throw $e;
        }
    }

    protected function getEmptyObject()
    {
        $item = $this->_className;
        return new $item();
    }

    /**
     * Default Functions!
     */
    public function setScriptsPath($scripts_path)
    {
        $this->_view->setScriptPath($scripts_path);
        return $this;
    }

    public function setControllerName($controllerName)
    {
        $this->_controllerName = $controllerName;
        return $this;
    }

    public function setView($view)
    {
        $this->_view = $view;
        $this->_view->moduleAdminPath = "news/news";
        return $this;
    }
    
    /**
     *
     * @param object $element
     * @param string $viewlabel
     * @param type $phtml
     * @return type 
     * 
     * 
     * viewlabel is the label with which you can get element from the phtml, that's
     * you can get this->$viewlabel in the phtml
     * 
     */
//    public function renderElement(&$element, $viewlabel, $phtml)
//    {
//        $this->_view->$viewlabel = $element;
//        //return $this->_view->render($phtml);
//    }

//  non funziona molto.. :(    
//    public function renderElement(&$element, $pthml)
//    {
//       $zv = new Zend_View();
//       $zv->setScriptPath(News::getModulePath().'views/scripts/');
//       $zv->element = $element;
//       $content = $zv->render('news/form.phtml');
//       Logger::getLogger(__CLASS__)->debug($zv);
//       return $content;
//    }


    public function getForm($formName, $options, $formData)
    {
        try {
            if (!class_exists($formName))
                throw new Exception("class $formName not found");
            if (!empty($options))
                $form = new $formName($options);
            else
                $form = new $formName(array('action' => 'save'));

            $formData = (empty($formData)) ? $this->getEmptyObject()->_toMap() : $formData;
            $form->populate($formData);
            return $form;
            // questo funziona quando vuoi fare il render dell'oggetto
            //     $this->_view->form = $form;
            //    return $this->_view->render($this->getPhtmlPath('form.phtml'));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function assignToView($label, &$content)
    {
        $this->_view->$label = $content;
    }
    
    public function drawPagination($curpage, $ipp, $num_elements, $baselink)
    {
        $end = ceil($num_elements / $ipp);
        $pagination = new stdClass();
        $pagination->curPage = $curpage;
        $pagination->start = 1;
        $pagination->end = $end;
        $pagination->linkTpl = $baselink.'pag/%d';
        $this->_view->pagination = $pagination;
    }

}

?>