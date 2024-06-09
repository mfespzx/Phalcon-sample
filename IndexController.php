<?php

class IndexController extends ControllerBase
{

    public function beforeExecuteRoute()
    {
    }

    public function initialize()
    {
    }

    public function indexAction()
    {
        return $this->dispatcher->forward(array(
            'controller' => 'admin',
            'action' => 'index'
        ));
    }

    public function homeAction()
    {
    }


}

