<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initViewItem() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

    protected function _initJQuery() {
        $view = $this->getResource('view');
        ZendX_JQuery::enableView($view);
        $view->jQuery()->setLocalPath('/js/admin/jquery.js')
                ->setUILocalPath('/js/admin/jquery-ui.js')
                ->addStyleSheet('/js/admin/css/ui-lightness/jquery-ui-1.8.13.custom.css')
                ->enable()
                ->uiEnable();
        $view->addHelperPath('WeDo/View/Helper/JQuery', 'WeDo_View_Helper_JQuery_');
    }

    protected function _initDoctype() {

        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    protected function _initJs() {
        $view = $this->getResource('view');

        $view->headScript()->appendFile("/js/admin/main.js")
                ->appendFile("/js/admin/zoombox/zoombox.js")
                ->appendFile("/js/admin/jwysiwyg/jquery.wysiwyg.js")
                ->appendFile("/js/admin/iphone-style-checkboxes.js")
                ->appendFile("/js/admin/jquery.uniform.js")
                ->appendFile("/js/admin/cookie/jquery.cookie.js")
                ->appendFile("/js/admin/tooltipsy.min.js");
    }

    protected function _initNavigation() {
        try {
            $this->bootstrap("layout");
            $layout = $this->getResource('layout');
            $view = $layout->getView();
            $admin_menu_desc = APPLICATION_PATH . '/configs/navigation.xml';
            if (!file_exists($admin_menu_desc))
                throw new Exception("file $admin_menu_desc not found");
            $config = new Zend_Config_Xml($admin_menu_desc, 'nav');
            $navigation = new Zend_Navigation($config);
            $view->navigation($navigation);
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function _initWeDo() {
        if (!defined('APP_PATH'))
            define('APP_PATH', APPLICATION_PATH . DIRECTORY_SEPARATOR . "../");
        WeDo_Application::runFor('backend');
    }

    /*
      protected function _initControllers() {
      $front = Zend_Controller_Front::getInstance();
      $front->setControllerDirectory(
      array(
      'default' => APPLICATION_PATH . '/modules/core/default/controllers',
      'admin' => APPLICATION_PATH . '/modules/core/admin/controllers',
      'library' => APPLICATION_PATH . '/modules/core/library/controllers',
      'pages' => APPLICATION_PATH . '/modules/local/pages/controllers',
      'clubs' => APPLICATION_PATH . '/modules/local/clubs/controllers'
      )
      );
      }

      protected function _initShantyMongo() {
      $connection = new Shanty_Mongo_Connection('mongodb://fit2me:fit2me@flame.mongohq.com:27069/fit2me');
      Shanty_Mongo::addMaster($connection);
      }

      protected function _initRoutes() {

      $frontController = Zend_Controller_Front::getInstance();
      $frontController->registerPlugin(new WeDo_Plugins_Auth_Check());
      $router = $frontController->getRouter();
      //if no user is logged, forwards to login, else forwards to dash
      $routeAdmin = new Zend_Controller_Router_Route(
      '/admin',
      array(
      'module' => 'admin',
      'controller' => 'index',
      'action' => 'index'
      )
      );

      $routeLogin = new Zend_Controller_Router_Route(
      '/admin/login',
      array(
      'module' => 'default',
      'controller' => 'login',
      'action' => 'login'
      )
      );

      $routeDefault = new Zend_Controller_Router_Route(
      '/admin/:controller',
      array(
      'module' => 'admin',
      'action' => 'index'
      )
      );

      $routeModules = new Zend_Controller_Router_Route(
      '/admin/:module/:controller/:action/*',
      array(
      'action' => 'index',
      'module' => 'pages',
      )
      );




      $router->addRoute('admin', $routeAdmin)
      ->addRoute('login', $routeLogin)
      ->addRoute('modules', $routeModules)
      ->addRoute('default', $routeDefault);
      }
     */
}

