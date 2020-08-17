<?php

namespace app\base;

class Session
{
    /**
     * @var int $expired
     */
    protected $expired = 86400; // 1*24*60*60 seconds, default - 1 day

    public function init()
    {
        if (!$this->isActive()) {
            session_save_path(APP_PATH . 'sessions/');
            session_start();
        }

        if (!$this->get('_CREATED')) {
            $this->set('_CREATED', time());
        }
        elseif ((time() - $this->get('_CREATED')) > $this->expired) {
            session_regenerate_id(true);
            $this->set('_CREATED', time());
        }

        return $this;
    }

    public function isActive()
    {
        return (session_status() === PHP_SESSION_ACTIVE);
    }

    public function id()
    {
        return session_id();
    }

    public function has($prop)
    {
        return isset($_SESSION[$prop]);
    }

    public function get($prop = null, $default = null)
    {
        if (!$prop) {
            return (isset($_SESSION) ? $_SESSION : []);
        }
        return ($this->has($prop) ? $_SESSION[$prop] : $default);
    }

    public function set($prop, $val)
    {
        $_SESSION[$prop] = $val;

        return $this;
    }

    public function rm($prop)
    {
        if ($this->has($prop)) {
            unset($_SESSION[$prop]);
        }

        return $this;
    }

    public function hasFlash($prop)
    {
        return $this->has('_FLASH-' . $prop);
    }

    public function getFlash($prop, $default = null)
    {
        if (!$this->hasFlash($prop)) {
            return $default;
        }

        $value = $this->get('_FLASH-' . $prop);
        $this->rmFlash($prop);

        return $value;
    }

    public function setFlash($prop, $value)
    {
        return $this->set('_FLASH-' . $prop, $value);
    }

    public function rmFlash($prop)
    {
        return $this->rm('_FLASH-' . $prop);
    }
}