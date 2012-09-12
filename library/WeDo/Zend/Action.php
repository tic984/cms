<?php

class Devel_Controller_Action extends Zend_Controller_Action
{

    protected $_modulePath;
    protected $_moduleName;
    protected $_className;
    protected $_controllerName;
    protected $_classUri;
    protected $_concreteHelper;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
    }

    public function init($concreteHelperName, $modulePath)
    {
//        Logger::getLogger(__CLASS__)->debug($modulePath." <----");
        $this->_modulePath = $modulePath;
        $this->_init();
        $this->_initHelper($concreteHelperName);
        $this->_initTranslator();
        $this->_initParams();
        $this->_helper->layout = Zend_Layout::getMvcInstance();
        $this->view->currentUser = Zend_Auth::getInstance()->getIdentity();
    }

    private function _init()
    {
        $this->_moduleName = ucfirst($this->getRequest()->getModuleName());
        $this->_controllerName = $this->getRequest()->getControllerName();
        $this->_className = ucfirst($this->_controllerName) . "Object";
        $this->_classUri = WeDo_Helpers_Application::getClassUri($this->_moduleName, $this->_className);
    }

    private function _initHelper($concreteHelperName)
    {
        Zend_Controller_Action_HelperBroker::addPath($this->_modulePath . 'views/helpers');
        $this->_concreteHelper = $this->_helper->$concreteHelperName;
        $this->_concreteHelper->setView($this->view)
                ->setScriptsPath($this->_modulePath . 'views/scripts/')
                ->setControllerName($this->_controllerName);
        $this->view->headScript()->appendFile(WeDo_Application::getSingleton('app/environment')->getAdminUrl() . 'common/js/min.js')
                ->appendFile(WeDo_Application::getSingleton('app/environment')->getAdminUrl() . 'common/js/main.js')
                ->appendFile(WeDo_Application::getSingleton('app/environment')->getAdminUrl() . 'common/content/settings/main.js');
    }

    private function _initTranslator()
    {
        $translator = new Zend_Translate('array', $this->_modulePath . 'dic/lang_it.php', 'it');
        Zend_Registry::set('Zend_Translate_Login', $translator);
        Zend_Validate_Abstract::setDefaultTranslator($translator);
        Zend_Form::setDefaultTranslator($translator);
        $this->view->translator = $translator;
    }

    protected function getUrl($action, $arr_params = array())
    {
        $base = sprintf("%s/%s/%s", strtolower($this->_moduleName), $this->_controllerName, $action);
        if (!empty($arr_params))
        {
            foreach ($arr_params as $k => $v)
                $base .= sprintf("/%s/%s", $k, urlencode($v));
        }
        return $base;
    }

    private function _initParams()
    {
        $items_per_page = $this->getModuleDescriptor()->getClassBackendPageProperty($this->_className, 'index', 'itemsInList');
        $list_is_sortable = $this->getModuleDescriptor()->getClassBackendPageProperty($this->_className, 'index', 'isListSortable');
        $sorting = $this->getModuleDescriptor()->getClassBackendPageProperty($this->_className, 'index', 'sorting');
        $this->_defaultParams['index'] = array('ipp' => $items_per_page, 'sortable' => $list_is_sortable, 'sorting' => $sorting);
    }

    protected function _default($param, $action)
    {
        return $this->_defaultParams[$action][$param];
    }

    public function getModuleDescriptor()
    {
        try {
            return WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getModuleDescriptor($this->_moduleName);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function indexAction()
    {
        $action = 'index';
        $page = $this->_getParam('pag', 1);
        $items_per_page = $this->_getParam('ipp', $this->_default('ipp', $action));
        $lang = $this->_getParam('lang', 'it');
        $start = ($page - 1) * $items_per_page;
       
    }

    public function getAnEmptyObject()
    {
        $class = $this->_className;
        return new $class();
    }

    public function getClassUri()
    {
        return $this->_classUri;
    }

    public function getClassName()
    {
        return $this->_className;
    }

    public function getActionUrl($relpath)
    {
        return $this->view->baseUrl($this->getUrl($relpath));
    }

    public function __call($name, $arguments)
    {
        print("Errore, chiamata: $name con argomenti:");
        print_r($arguments);
    }

    /**
     *
     * @param type $layoutplaceholder
     * @param type $content
     * @param type $phtml 
     * @deprecated
     * mmmmmmmm.... 
     */
    public function assignLayoutContent($layoutplaceholder, &$content, $phtml)
    {
        $this->_helper->layout->$layoutplaceholder = $this->_concreteHelper->assignAndRender($content, $phtml);
    }

    /**
     *
     * @param type $layoutplaceholder
     * @param type $phtml 
     * 
     * Renders a phtml to a placeholder.
     * Can be used if and only if there's no dynamic content inside. Phtml path will be resolved by Helper.
     */
    public function renderLayoutContent($layoutplaceholder, $phtml)
    {
        $this->_helper->layout->$layoutplaceholder = $this->_concreteHelper->renderIt($phtml);
    }

    /**
     *
     * @param type $layoutplaceholder
     * @param type $content 
     * 
     * Sets layout variables, that is assign it to $this->_helper->layout.
     * These variables needs to be rendered to a view scripts BEFORE assigning them!
     */
    public function setLayoutVar($layoutplaceholder, &$content)
    {
        $this->_helper->layout->$layoutplaceholder = $content;
    }

}

?>