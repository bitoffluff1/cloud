<?php


namespace App\controllers;

use App\main\App;

class AuthController extends Controller
{
    protected $defaultAction = "auth";

    public function authAction()
    {
        $params = [
            "user" => $this->checkUser(),
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

        if(App::call()->authServices->login($this->request, $user->columns, $password)){

            $params = [
                "user" => $user->columns,
            ];
            echo $this->render("auth", $params);

        } else {
            $this->redirect();
        }
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
                "user" => $this->checkUser(),
                "message" => "Логин {$login} уже используется!",
            ];
            echo $this->render("auth", $params);

            return;
        }

        $data = ["login" => $login, "password" => $password, "fio" => $fio];

        $user = $this->checkUser();
        if($user["role"] === "isAdmin"){
            $data["role"] = "isAdmin";
        }

        $newUser = App::call()->authServices->addUser($this->request, $data);

        $params = [
            "user" => $newUser,
        ];
        echo $this->render("auth", $params);
    }

    public function logoutAction()
    {
        App::call()->authServices->logout($this->request);
        $this->redirect("/auth");
    }

    public function getUserAction(){
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            echo $user = json_encode($this->checkUser());
        }
    }
}