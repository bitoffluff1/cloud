<?php


namespace App\services;

use App\main\App;

class FolderServices
{
    public function addFolder($id_user)
    {
        $path = "/../files/{$id_user}";
        if (file_exists(PUBLIC_DIR . $path . "/Новая папка")) {
            $files = scandir(PUBLIC_DIR . $path);
            $arr = [];
            foreach ($files as $name) {
                if (preg_match("/^Новая папка[0-9]?$/", $name, $matches)) {
                    $arr[] = $matches[0];
                }
            }
            $length = count($arr);
            mkdir(PUBLIC_DIR . $path . "/Новая папка{$length}");
            $name = "Новая папка{$length}";
        } else {
            mkdir(PUBLIC_DIR . $path . "/Новая папка");
            $name = "Новая папка";
        }

        return ["path" => $path, "name" => $name];
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

    public function renameFile($id_user, $name, $newName)
    {
        $path = PUBLIC_DIR . "/../files/{$id_user}";

        if (file_exists($path . "/{$name}")) {
            rename($path . "/{$name}", $path . "/{$newName}");
        }

        if(file_exists($path . "/{$newName}")){
            return true;
        } else{
            return false;
        }
    }

    public function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    public function deleteFile($id_user, $name)
    {
        $file = PUBLIC_DIR . "/../files/{$id_user}/{$name}";

        $this->removeDirectory($file);

        if(!file_exists($file)){
            return true;
        } else{
            return false;
        }
    }
}