<?php

namespace shenoda\phpmvc;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            //mark them to be removed in the destructor
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;

    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'value' => $message,
            'remove' => false
        ];
    }

    public function getFlash($key)
    {

        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        // Iterate over marked messages to be removed.
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}