<?php


namespace App\main;

use App\models\repositories\FileRepository;
use App\services\AuthServices;
use \App\services\renders\TwigRender;
use App\services\FileServices;
use App\services\Db;

use App\traits\TSingleton;

/**
 * Class App
 * @package App\main
 *
 * @property Db $db
 * @property TwigRender $render
 * @property FileServices $fileServices
 * @property FileRepository $fileRepository
 * @property AuthServices $authServices
 */
class App
{
    use TSingleton;

    /**
     * Возвращает объект класса App
     * @return App
     */
    static public function call(): App
    {
        return static::getInstance();
    }

    public $config = [];
    public $components = [];

    public function run($config)
    {
        $this->config = $config;
        $this->runController();
    }

    private function runController()
    {
        $request = new \App\services\Request();
        $controllerName = $request->getControllerName() ?: "auth";
        $actionName = $request->getActionName();

        $controllerName = "App\\controllers\\" . ucfirst($controllerName) . "Controller";
        $render = App::call()->render;

        if (class_exists($controllerName)) {
            $controller = new $controllerName($render, $request);
            $controller->run($actionName);
        } else {
            echo $render->render("layouts/error", $params = []);
        }
    }

    /**
     * Создает объект, если объект уже создан, то возвращает его
     * @param string $name - название одного из классов указанных в components
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->components)) {
            return $this->components[$name];
        }

        if (array_key_exists($name, $this->config["components"])) {
            $class = $this->config["components"][$name]["class"];

            if (!class_exists($class)) {
                return null;
            }

            if (array_key_exists("config", $this->config["components"][$name])) {
                $config = $this->config["components"][$name]["config"];
                $component = new $class($config);
            } else {
                $component = new $class();
            }

            $this->components[$name] = $component;
            return $component;
        }
        return null;
    }
}