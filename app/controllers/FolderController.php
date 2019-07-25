<?php


namespace App\controllers;

use App\main\App;

class FolderController extends Controller
{
    public function addAction()
    {
        $this->isUser();

        $path = App::call()->fileServices->getPathFile($this->checkUser());

        if (!empty($this->getId())) {
            $folder = App::call()->folderRepository->getOne($this->getId());
            $path = App::call()->folderServices->getPathFolder($folder->columns);
        }

        if (empty($path)) {
            $this->redirect();
        }

        $nameFolder = App::call()->folderServices->addFolder($path);
        if (!empty($nameFolder)) {
            $params["name"] = $nameFolder;
            $params["path"] = $path;
            App::call()->folderServices->changeFile($params);
        }

        $this->redirect();
    }

    public function changeAction()
    {
        $this->isUser();

        $params = $this->request->getParams("post");
        if (!isset($params["id"]) && !isset($params["name"])) {
            $this->redirect();
        }

        App::call()->folderServices->changeFile(["id" => $params["id"], "mark" => "changeName"]);

        $folder = App::call()->folderRepository->getOne($params["id"]);
        foreach (["file", "folder"] as $entity){
            App::call()->folderServices->renamePath($folder, $params["name"], $entity);
        }

        if (App::call()->folderServices->renameFile($folder->columns, $params["name"]) &&
            $folder->columns["mark"] === "changeName") {
            App::call()->folderServices->changeFile($params);
        }

        App::call()->folderServices->changeFile(["id" => $params["id"], "mark" => 1]);

        $this->redirect();
    }

    public function deleteAction()
    {
        $this->isUser();

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