<?php


namespace App\controllers;


use App\main\App;
use App\services\renders\IRender;
use App\services\Request;

abstract class Controller
{
    private $action;
    protected $request;
    protected $defaultAction = "index";

    protected $render;

    public function __construct(IRender $render, Request $request)
    {
        $this->request = $request;
        $this->render = $render;
    }

    public function run($action)
    {
        $this->action = $action ?: $this->defaultAction;
        $method = $this->action . "Action";
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            echo $this->render->render("layouts/error", $params = []);
        }
    }

    public function render($template, $params = [])
    {
        $content = $this->renderTmpl($template, $params);
        return $this->renderTmpl("layouts/main", ["content" => $content, "user" => $this->checkUser()]);
    }

    /**
     * @param $template
     * @param array $params ["name" => "user"]
     * @return
     */
    public function renderTmpl($template, $params = [])
    {
        return $this->render->render($template, $params);
    }

    protected function getId()
    {
        return (int)$this->request->getParams("get", "id");
    }

    public function redirect($path = "")
    {
        if (empty($path)) {
            $path = $_SERVER["HTTP_REFERER"];
        }
        header("Location: " . $path);
    }


    public function checkUser()
    {
        $user = $this->request->getSession("user");

        return $user["id"];
    }


    public function checkData($data){
        foreach ($data as $key => $value) {
            if (empty($value)) {
               return false;
            }
        }
        return true;
    }


}