<?php


namespace App\services;

use App\main\App;

class FolderServices
{
    public function getPathFolder(array $folder)
    {
        return "{$folder["path"]}/{$folder["name"]}";
    }

    public function addFolder($path)
    {
        if (!file_exists(PUBLIC_DIR . $path)) {
            mkdir(PUBLIC_DIR . $path);
        }

        if (file_exists(PUBLIC_DIR . $path . "/Новая папка")) {
            $files = scandir(PUBLIC_DIR . $path);
            $arr = [];
            foreach ($files as $name) {
                if (preg_match("/^Новая папка(\([0-9]\))?$/", $name, $matches)) {
                    $arr[] = $matches[0];
                }
            }
            $length = count($arr);
            $name = "Новая папка({$length})";
            mkdir(PUBLIC_DIR . $path . "/" . $name);
        } else {
            $name = "Новая папка";
            mkdir(PUBLIC_DIR . $path . "/" . $name);
        }

        if (file_exists(PUBLIC_DIR . $path . "/" . $name)) {
            return $name;
        }
        return false;
    }

    public function getFolder($id_user)
    {
        $path = PUBLIC_DIR . "/../files/{$id_user}";
        $files = scandir($path);
        $result = [];
        foreach ($files as $value) {
            if ($value === '.' or $value === '..') continue;

            if (is_dir($path . '/' . $value)) {
                $result[] = $value;
            }
        }
        return $result;
    }

    public function changeFile($data)
    {
        $file = App::call()->folderRepository->newEntity($data);
        App::call()->folderRepository->save($file);
    }

    public function renameFile($folder, $newName)
    {
        $path = PUBLIC_DIR . $folder["path"];

        if (file_exists($path . "/{$folder["name"]}")) {
            rename($path . "/{$folder["name"]}", $path . "/{$newName}");
        }

        if (file_exists($path . "/{$newName}")) {
            return true;
        }
        return false;
    }


    public function getLikeItems($path, $entity)
    {
        if ($entity === "file") {
            $items = App::call()->fileRepository->getAllLike("{$path}%");
        } else if ($entity === "folder") {
            $items = App::call()->folderRepository->getAllLike("{$path}%");
        }

        $array = [];
        foreach ($items as $item) {
            foreach ($item as $value) {
                $array[] = $value;
            }
        }
        return $array;
    }

    public function renamePath($folder, $newName, $entity)
    {
        $path = "{$folder->columns["path"]}/{$folder->columns["name"]}";
        $newPath = "{$folder->columns["path"]}/{$newName}";

        $items = $this->getLikeItems($path, $entity);

        foreach ($items as $value) {
            $data["id"] = $value["id"];

            if ($value["path"] === $path) {
                $data["path"] = $newPath;
            } else {
                $arrayPath = explode("/", $value["path"]);
                $arrNewPath = explode("/", $newPath);

                $data["path"] = implode("/", array_replace($arrayPath, $arrNewPath));
            }

            if ($entity === "file") {
                App::call()->fileServices->changeFile($data);
            } else if ($entity === "folder") {
                App::call()->folderServices->changeFile($data);
            }

        }
    }

    public function removeDirectory($dir)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }


    public function deleteFile($path, $name)
    {
        $file = PUBLIC_DIR . $path . "/{$name}";

        $this->removeDirectory($file);

        if (!file_exists($file)) {
            return true;
        }
        return false;
    }

    public function deleteFileDb($folder, $entity)
    {
        $path = "{$folder->columns["path"]}/{$folder->columns["name"]}";

        $items = $this->getLikeItems($path, $entity);

        foreach ($items as $value) {
            if ($entity === "file") {
                $file = App::call()->fileRepository->newEntity(["id" => $value["id"]]);
                App::call()->fileRepository->delete($file);
            } else if ($entity === "folder") {
                $file = App::call()->folderRepository->newEntity(["id" => $value["id"]]);
                App::call()->folderRepository->delete($file);
            }
        }
    }
}