<?php

namespace shenoda\phpmvc;

use shenoda\phpmvc\db\Database;
use shenoda\phpmvc\db\DbModel;

class Application
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';
    protected array $eventListeners = [];

    public string $layout = "main";
    public ?string $userClass = null;
    public Request $request;
    public Router $router;
    public Response $response;
    public Session $session;
    public View $view;
    public Database $db;
    public static $ROOT_DIR;
    public static Application $app;
    public ?Controller $controller = null;
    public ?UserModel $user;

    public function __construct($rootPath, array $config)
    {

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->userClass = $config['userClass'] ?? null;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session();
        $this->view = new View();

        if (!isset($config['db'])) {
            throw new \InvalidArgumentException('Database configuration is required');
        }
        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue && $this->userClass) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }

    public function getController(): Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user::primaryKey(); // "id"
        $primaryValue = $user->{$primaryKey};

        $this->session->set("user", $primaryValue);

        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove("user");
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function run(): void
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());

            echo $this->view->renderView('_error', ['exception' => $e]);
        }
    }

    public function on($eventName, $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }

    public function triggerEvent($eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }
}