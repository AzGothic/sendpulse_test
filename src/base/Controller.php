<?php

namespace app\base;

class Controller
{
    /**
     * @var Request Request
     */
    public $request;

    /**
     * @var Response Response
     */
    public $response;

    public function __construct()
    {
        $this->request = Application::$request;
        $this->response = Application::$response;
    }
}