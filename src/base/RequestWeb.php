<?php

namespace app\base;

class RequestWeb extends Request
{
    /**
     * Session
     * @var Session $session
     */
    public $session;

    public function __construct()
    {
        parent::__construct();

        $this->session = new Session();
        $this->session->init();
    }
}