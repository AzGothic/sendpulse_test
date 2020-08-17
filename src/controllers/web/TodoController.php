<?php

namespace app\controllers\web;

use app\base\helpers\ArrayHelper;
use app\models\Task;

class TodoController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->user->isGuest()) {
            $this->response->redirect('/user/login');
        }
    }

    public function IndexAction()
    {
        $taskModel = new Task();
        $this->response->view['tasks'] = $taskModel->getTasks(0, $this->user->user['id']);
    }

    public function ViewAction()
    {
        if (!isset($this->request->_GET['id']) || !$id = $this->request->_GET['id']) {
            $this->request->session->setFlash('error_message', 'Wrong request!');
            $this->response->redirect('/todo', 301);
            return false;
        }
        $taskModel = new Task();
        if (!$task = $taskModel->getTask($id, $this->user->user['id'])) {
            $this->request->session->setFlash('error_message', 'Task not found!');
            $this->response->redirect('/todo', 301);
            return false;
        }
        $this->response->view['task'] = $task;

        if ($task['parent_id'] == 0) {
            $this->response->view['subs'] = $taskModel->getTasks($task['id'], $this->user->user['id']);
        }
    }

    public function DoneAction()
    {
        if (!isset($this->request->_GET['id']) || !$id = $this->request->_GET['id']) {
            $this->request->session->setFlash('error_message', 'Wrong request!');
            $this->response->redirect('/todo', 301);
            return false;
        }

        $taskModel = new Task();
        if (!$taskModel->markAsDone($id, $this->user->user['id'])) {
            $this->request->session->setFlash('error_message', 'Something went wrong!');
            $this->response->redirect('/todo/view?id=' . $id);
            return false;
        }

        $this->request->session->setFlash('success_message', 'Task Closed successfully');
        $this->response->redirect('/todo/view?id=' . $id);
    }

    public function RemoveAction()
    {
        if (!isset($this->request->_GET['id']) || !$id = $this->request->_GET['id']) {
            $this->request->session->setFlash('error_message', 'Wrong request!');
            $this->response->redirect('/todo', 301);
            return false;
        }

        $taskModel = new Task();
        if (!$task = $taskModel->getTask($id, $this->user->user['id'])) {
            $this->request->session->setFlash('error_message', 'Task not found!');
            $this->response->redirect('/todo', 301);
            return false;
        }
        if (!$taskModel->remove($id, $this->user->user['id'])) {
            $this->request->session->setFlash('error_message', 'Something went wrong!');
            $this->response->redirect('/todo/view?id=' . $id);
            return false;
        }

        $this->request->session->setFlash('success_message', 'Task Deleted successfully');
        $this->response->redirect('/todo' . ($task['parent_id'] ? '/view?id=' . $task['parent_id'] : ''));
    }

    public function AddAction()
    {
        $post = $this->request->_POST;
        $this->response->view = $post;

        $parent_id = (!empty($this->request->_GET['parent_id']) ? $this->request->_GET['parent_id'] : 0);
        $this->response->view['parent_id'] = $parent_id;

        if (!$post) {
            return true;
        }

        $taskModel = new Task();
        if (!$taskModel->create(ArrayHelper::merge([
            'user_id' => $this->user->user['id'],
            'parent_id' => $parent_id,
        ], $post))) {
            $this->request->session->setFlash('error_message', 'Something went wrong!');
            return false;
        }

        $this->request->session->setFlash('success_message', 'Task Created successfully');
        $this->response->redirect('/todo' . ($parent_id ? '/view?id=' . $parent_id : ''));
    }

    public function EditAction()
    {
        if (!isset($this->request->_GET['id']) || !$id = $this->request->_GET['id']) {
            $this->request->session->setFlash('error_message', 'Wrong request!');
            $this->response->redirect('/todo', 301);
            return false;
        }
        $taskModel = new Task();
        if (!$task = $taskModel->getTask($id, $this->user->user['id'])) {
            $this->request->session->setFlash('error_message', 'Task not found!');
            $this->response->redirect('/todo', 301);
            return false;
        }

        $post = $this->request->_POST;
        $this->response->view = ArrayHelper::merge($task, $post);

        if (!$post) {
            return true;
        }

        $taskModel = new Task();
        if (!$taskModel->update(ArrayHelper::merge([
            'user_id' => $this->user->user['id'],
            'id' => $task['id'],
        ], $post))) {
            $this->request->session->setFlash('error_message', 'Something went wrong!');
            return false;
        }

        $this->request->session->setFlash('success_message', 'Task Updated successfully');
        $this->response->redirect('/todo/view?id=' . $task['id']);
    }
}