<?php


namespace App\controllers;

use App\main\App;

class FolderController extends Controller
{
    public function indexAction()
    {
        $id_folder = $this->getId();
        $sql = "SELECT * FROM `files` WHERE `id_user` = '{$this->checkUser()}' AND `id_folder` = '{$id_folder}'";

        $params = [
            "user" => $this->checkUser(),
            "folder" => $id_folder,
            "files" => App::call()->fileRepository->getAll($this->checkUser(), $sql),
        ];

        echo $this->render("folder", $params);
    }

    public function addAction()
    {
        $data = App::call()->folderServices->addFolder($this->checkUser());

        $params["id_user"] = $this->checkUser();
        $params["path"] = $data["path"];
        $params["name"] = $data["name"];

        if ($this->checkData($params)) {
            App::call()->folderServices->changeFile($params);
        }

        $this->redirect();
    }

    public function changeAction()
    {
        $params = $this->request->getParams("post");
        if (!isset($params["id"]) && !isset($params["name"])) {
            $this->redirect();
        }

        App::call()->folderServices->changeFile(["id" => $params["id"], "mark" => "changeName"]);

        $file = App::call()->folderRepository->getOne($params["id"]);
        if (App::call()->folderServices->renameFile($this->checkUser(), $file->columns["name"], $params["name"]) &&
            $file->columns["mark"] === "changeName") {
            App::call()->folderServices->changeFile($params);
        }

        App::call()->folderServices->changeFile(["id" => $params["id"], "mark" => 1]);

        $this->redirect();
    }

    public function deleteAction()
    {
        App::call()->folderServices->changeFile(["id" => $this->getId(), "mark" => "delete"]);
        $file = App::call()->folderRepository->getOne($this->getId());

        if (App::call()->folderServices->deleteFile($this->checkUser(), $file->columns["name"]) &&
            $file->columns["mark"] === "delete") {
            $file = App::call()->folderRepository->newEntity(["id" => $this->getId()]);
            App::call()->folderRepository->delete($file);
        }

        $this->redirect();
    }

}