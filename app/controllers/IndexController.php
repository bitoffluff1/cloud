<?php


namespace App\controllers;

use App\main\App;

class IndexController extends Controller
{
    protected $defaultAction = "index";

    public function indexAction()
    {
        $sql = "SELECT * FROM `files` WHERE `id_user` = '{$this->checkUser()}' AND `id_folder` IS NULL";

        $params = [
            "user" => $this->checkUser(),
            "files" => App::call()->fileRepository->getAll($this->checkUser(), $sql),
            "folder" => App::call()->folderRepository->getAll($this->checkUser())
        ];

        echo $this->render("index", $params);
    }

    public function addAction()
    {
        $id_user = $this->checkUser();
        $file = $this->request->getFile();
        $arr = explode(".", $file["name"]);

        $id_folder = $this->request->getParams("post", "id");
        if (!empty($id_folder)) {
            $params["id_folder"] = $id_folder;
            $folder = App::call()->folderRepository->getOne($id_folder);
            $path = App::call()->fileServices->copyFile($id_user, $file, $folder->columns["name"]);
        } else {
            $path = App::call()->fileServices->copyFile($id_user, $file);
        }

        $params["id_user"] = $id_user;
        $params["name"] = $arr[0];
        $params["filename_extension"] = $arr[1];
        $params["path"] = $path;

        if ($this->checkData($params)) {
            App::call()->fileServices->changeFile($params);
        }

        $this->redirect();
    }

    public function changeAction()
    {
        $params = $this->request->getParams("post");
        if (!isset($params["id"]) && !isset($params["name"])) {
            $this->redirect();
        }
        App::call()->fileServices->changeFile(["id" => $params["id"], "mark" => "changeName"]);

        $file = App::call()->fileRepository->getOne($params["id"]);
        if (App::call()->fileServices->renameFile($file->columns["path"], $file->columns["name"],
                $file->columns["filename_extension"], $params["name"]) &&
            $file->columns["mark"] === "changeName") {
            App::call()->fileServices->changeFile($params);
        }

        App::call()->fileServices->changeFile(["id" => $params["id"], "mark" => 1]);

        $this->redirect();
    }

    public function deleteAction()
    {
        App::call()->fileServices->changeFile(["id" => $this->getId(), "mark" => "delete"]);
        $file = App::call()->fileRepository->getOne($this->getId());

        if (App::call()->fileServices->deleteFile($file->columns["path"], $file->columns["name"],
                $file->columns["filename_extension"]) &&
            $file->columns["mark"] === "delete") {
            $file = App::call()->fileRepository->newEntity(["id" => $this->getId()]);
            App::call()->fileRepository->delete($file);
        }

        $this->redirect();
    }

    public function downloadAction()
    {
        $item = App::call()->fileRepository->getOne($this->getId());
        $file = PUBLIC_DIR . $item->columns["path"] . "/{$item->columns['name']}.{$item->columns['filename_extension']}";

        if (file_exists($file)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            readfile($file);
        }

    }

}