<?php

class Admin_StatsController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $this->view->headScript()->appendFile("/js/admin/excanvas.js");
    }

}

