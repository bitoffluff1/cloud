<?php


namespace App\controllers;

use App\main\App;

class AuthController extends Controller
{
    protected $defaultAction = "auth";

    public function authAction()
    {
        $params = [
            "user" => $this->request->getSession("user"),
        ];

        echo $this->render("auth", $params);
    }

    public function loginAction()
    {
        $login = $this->request->getParams("post", "login");
        $password = $this->request->getParams("post", "password");
        if (empty($login) && empty($password)) {
            $this->redirect();
            return;
        }

        $user = App::call()->userRepository->getUser($login);
        if (empty($user)) {
            $this->redirect();
            return;
        }

        if (App::call()->authServices->login($this->request, $user->columns, $password)) {
            $this->redirect("/index");
            return;
        }

        $this->redirect();
    }

    public function signUpAction()
    {
        $password = App::call()->authServices->getPassword($this->request);
        $login = $this->request->getParams("post", "login");
        $fio = $this->request->getParams("post", "fio");
        if (empty($login) && empty($password)) {
            $this->redirect();
            return;
        }

        if (App::call()->authServices->checkBusyLogin($login)) {
            $params = [
                "message" => "Логин {$login} уже используется!",
            ];
            echo $this->render("auth", $params);
            return;
        }

        $data = ["login" => $login, "password" => $password, "fio" => $fio];

        if (App::call()->authServices->addUser($this->request, $data)) {
            $this->redirect("/index");
            return;
        }

        $this->redirect();
    }

    public function logoutAction()
    {
        App::call()->authServices->logout($this->request);
        $this->redirect("/auth");
    }
}