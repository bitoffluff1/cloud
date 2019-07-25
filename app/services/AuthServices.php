<?php


namespace App\services;

use App\main\App;

class AuthServices
{
    public function getPassword($request)
    {
        return password_hash($request->getParams("post", "password"), PASSWORD_DEFAULT);
    }

    public function login($request, $user, $password)
    {
        if (password_verify($password, $user["password"])) {
            $request->setSession("user", $user);
            return true;
        }
        return false;
    }

    public function checkBusyLogin($login)
    {
        $user = App::call()->userRepository->getUser($login);
        return empty($user) ? false : true;
    }

    public function addUser($request, $data)
    {
        $newUser = App::call()->userRepository->newEntity($data);
        App::call()->userRepository->save($newUser);
        if(!empty($newUser)){
            $request->setSession("user", $newUser->columns);
            return true;
        }

        return false;
    }

    public function logout($request)
    {
        $request->destroySession();
    }
}