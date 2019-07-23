<?php


namespace App\services;


class Request
{
    private $requestString;
    private $controllerName;
    private $actionName;
    private $params;

    public function __construct()
    {
        session_start();

        $this->requestString = $_SERVER["REQUEST_URI"];
        $this->parseRequest();
    }

    /**
     * @return mixed
     */
    public function getRequestString()
    {
        return $this->requestString;
    }

    /**
     * @return mixed
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @return mixed
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    public function getParams(string $method, string $key = "")
    {
        if (empty($key)) {
            return $this->params[$method];
        }
        return array_key_exists($key, $this->params[$method])
            ? $this->params[$method][$key] : null;
    }

    public function parseRequest()
    {
        $pattern = "#(?P<controller>\w+)[/]?(?P<action>\w+)?[/]?[?]?(?P<params>.*)#ui";

        if (preg_match_all($pattern, $this->requestString, $matches)) {
            $this->controllerName = $matches["controller"][0];
            $this->actionName = $matches["action"][0];

            $this->params["post"] = $_POST;
            $this->params["get"] = $_GET;
        }

    }

    public function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function getSession(string $key = "")
    {
        if (empty($key)) {
            return $_SESSION;
        }
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : [];
    }

    public function clearSession($key){
        unset($_SESSION[$key]);
    }

    public function destroySession(){
        session_destroy();
    }

    public function getFile(){
        return $_FILES['userfile'];
    }
}