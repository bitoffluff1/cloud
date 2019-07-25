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

    public function renamePath($folder, $newName, $entity)
    {
        $path = "{$folder->columns["path"]}/{$folder->columns["name"]}";
        $newPath = "{$folder->columns["path"]}/{$newName}";
        $items = [];

        if ($entity === "file") {
            $items = App::call()->fileRepository->getAllLike("{$path}%");
        } else if ($entity === "folder") {
            $items = App::call()->folderRepository->getAllLike("{$path}%");
        }

        foreach ($items as $item) {
            foreach ($item as $value) {
                $data["id"] = $value["id"];
                $data["path"] = $this->getNewPath($path, $newPath);

                if ($entity === "file") {
                    App::call()->fileServices->changeFile($data);
                } else if ($entity === "folder") {
                    App::call()->folderServices->changeFile($data);
                }
            }
        }
    }

    public function getNewPath($string_1, $string_2)
    {
        $string_1_length = strlen($string_1);
        $string_2_length = strlen($string_2);
        $return = '';

        $longest_common_subsequence = array_fill(0, $string_1_length,
            array_fill(0, $string_2_length, 0));

        $largest_size = 0;

        for ($i = 0; $i < $string_1_length; $i++) {
            for ($j = 0; $j < $string_2_length; $j++) {
                if ($string_1[$i] === $string_2[$j]) {
                    if ($i === 0 || $j === 0) {
                        $longest_common_subsequence[$i][$j] = 1;
                    } else {
                        $longest_common_subsequence[$i][$j] = $longest_common_subsequence[$i - 1][$j - 1] + 1;
                    }

                    if ($longest_common_subsequence[$i][$j] > $largest_size) {
                        $largest_size = $longest_common_subsequence[$i][$j];
                        $return = '';
                    }

                    if ($longest_common_subsequence[$i][$j] === $largest_size) {
                        $return = substr($string_1, $i - $largest_size + 1, $largest_size);
                    }
                }
            }
        }

        return $return;
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

    public function deleteFile($id_user, $name)
    {
        $file = PUBLIC_DIR . "/../files/{$id_user}/{$name}";

        $this->removeDirectory($file);

        if (!file_exists($file)) {
            return true;
        } else {
            return false;
        }
    }
}