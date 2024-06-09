<?php

class AdminController extends ControllerBase
{

    public function beforeExecuteRoute()
    {
    }

    public function initialize()
    {
    }

    public function indexAction()
    {
    }

    public function homeAction()
    {
    }

    public function loginAction()
    {
        if ($this->request->isPost()) {
            $user = AdminUsers::findFirst(array(
                'name = :name: and password = :password:',
                'bind' => array(
                    'name' => $this->request->getPost("name"),
                    'password' => sha1($this->request->getPost("password"))
                )
            ));
            if ($user === false){
                $this->flash->error("Incorrect credentials");
                return $this->dispatcher->forward(array(
                    'controller' => 'admin',
                    'action' => 'index'
                ));
            }
            $this->session->set('auth', $user->id);
            $this->flash->success("You've been successfully logged in");
        }
        return $this->dispatcher->forward(array(
            'controller' => 'users',
            'action' => 'index'
        ));
    }

    public function logoutAction()
    {
        $this->session->remove('auth');
        return $this->dispatcher->forward(array(
            'controller' => 'admin',
            'action' => 'index'
        ));
    }


}

