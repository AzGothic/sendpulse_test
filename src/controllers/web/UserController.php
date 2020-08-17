<?php

namespace app\controllers\web;

use app\models\User;

class UserController extends Controller
{
    public function RegistrationAction()
    {
        if (!$this->user->isGuest()) {
            $this->response->redirect('/');
        }

        $post = $this->request->_POST;
        $this->response->view = $post;

        if (!$post) {
            return true;
        }

        if (!$this->user->registration($post)) {
            $this->request->session->setFlash('error_message', 'Something went wrong!');
            return false;
        }

        $this->response->redirect('/');
    }

    public function LoginAction()
    {
        if (!$this->user->isGuest()) {
            $this->response->redirect('/');
        }

        $post = $this->request->_POST;
        $this->response->view = $post;

        if (!$post) {
            return true;
        }

        if (!$this->user->login($post)) {
            $this->request->session->setFlash('error_message', 'Something went wrong!');
            return false;
        }

        $this->response->redirect('/');
    }

    public function LogoutAction()
    {
        $this->user->logout();
        $this->response->redirect('/');
    }
}