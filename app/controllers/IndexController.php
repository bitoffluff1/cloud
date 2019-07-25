<?php


namespace App\controllers;

use App\main\App;

class IndexController extends Controller
{
    protected $defaultAction = "index";

    public function indexAction()
    {
        $this->isUser();

        $path = App::call()->fileServices->getPathFile($this->checkUser());

        if (!empty($this->getId())) {
            $folder = App::call()->folderRepository->getOne($this->getId());
            $path = App::call()->folderServices->getPathFolder($folder->columns);
        }

        $params = [
            "files" => App::call()->fileRepository->getAll($path),
            "folder" => App::call()->folderRepository->getAll($path),
            "id_folder" => $folder->columns["id"]
        ];

        echo $this->render("index", $params);
    }

    public function addAction()
    {
        $this->isUser();

        $path = App::call()->fileServices->getPathFile($this->checkUser());

        $id_folder = $this->request->getParams("post", "id_folder");
        if (!empty($id_folder)) {
            $folder = App::call()->folderRepository->getOne($id_folder);
            $path = App::call()->folderServices->getPathFolder($folder->columns);
        }

        $file = $this->request->getFile();

        if (!App::call()->fileServices->checkFile($file)) {
            $this->redirect();
            return;
        }

        $arrFileName = explode(".", $file["name"]);
        $params["id_user"] = $this->checkUser();
        $params["filename_extension"] = $arrFileName[1];
        $params["path"] = $path;

        $nameFile = App::call()->fileServices->copyFile($path, $file, $arrFileName[0], $arrFileName[1]);
        if (!empty($nameFile) && $this->checkData($params)) {
            $params["name"] = $nameFile;
            //App::call()->fileServices->changeFile($params);
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

        App::call()->fileServices->changeFile(["id" => $params["id"], "mark" => "changeName"]);

        $file = App::call()->fileRepository->getOne($params["id"]);
        if (App::call()->fileServices->renameFile($file->columns, $params["name"]) &&
            $file->columns["mark"] === "changeName") {
            App::call()->fileServices->changeFile($params);
        }

        App::call()->fileServices->changeFile(["id" => $params["id"], "mark" => 1]);

        $this->redirect();
    }

    public function deleteAction()
    {
        $this->isUser();

        App::call()->fileServices->changeFile(["id" => $this->getId(), "mark" => "delete"]);
        $file = App::call()->fileRepository->getOne($this->getId());

        if (App::call()->fileServices->deleteFile($file->columns) &&
            $file->columns["mark"] === "delete") {
            $file = App::call()->fileRepository->newEntity(["id" => $this->getId()]);
            App::call()->fileRepository->delete($file);
        }

        $this->redirect();
    }

    public function downloadAction()
    {
        $this->isUser();

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