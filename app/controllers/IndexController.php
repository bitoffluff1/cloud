<?php


namespace App\controllers;

use App\main\App;

class IndexController extends Controller
{
    protected $defaultAction = "index";

    public function indexAction()
    {
        $params = [
            "user" => $this->checkUser(),
            "files" => App::call()->fileRepository->getAll($this->checkUser())
        ];

        echo $this->render("index", $params);
    }

    public function addAction()
    {
        $id_user = $this->checkUser();
        $file = $this->request->getFile();
        $arr = explode(".", $file["name"]);

        App::call()->fileServices->copyFile($id_user, $file);

        $params["id_user"] = $id_user;
        $params["name"] = $arr[0];
        $params["filename_extension"] = $arr[1];

        if ($this->checkData($params)) {
            App::call()->fileServices->changeFile($params);
        }

        $this->redirect();
    }

    public function changeAction()
    {
        $params = $this->request->getParams("post");

        $id_user = $this->checkUser();
        $file = App::call()->fileRepository->getOne($params["id"]);
        App::call()->fileServices->renameFile($id_user, $file->columns["name"], $file->columns["filename_extension"], $params["name"]);


        if ($this->checkData($params)) {
            App::call()->fileServices->changeFile($params);
        }

        $this->redirect();
    }

    public function deleteAction()
    {
        $id_user = $this->checkUser();
        $file = App::call()->fileRepository->getOne($this->getId());
        App::call()->fileServices->deleteFile($id_user, $file->columns["name"], $file->columns["filename_extension"]);

        $file = App::call()->fileRepository->newEntity(["id" => $this->getId()]);
        App::call()->fileRepository->delete($file);

        $this->redirect();
    }

}