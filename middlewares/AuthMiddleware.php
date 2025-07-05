<?php

namespace shenoda\phpmvc\middlewares;

use shenoda\phpmvc\Application;
use shenoda\phpmvc\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];

    /**
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }


    public function execute()
    {
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                //throw error forbidden access
                throw new ForbiddenException();
            }
        }
    }
}