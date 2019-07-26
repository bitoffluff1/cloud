<?php


namespace App\services;


use App\main\App;

class FileServices
{

    public function checkFile($file)
    {
        if (!is_uploaded_file($file['tmp_name'])) return false;

        $blacklist = [".php", ".phtml", ".php3", ".php4", ".html", ".htm"];
        foreach ($blacklist as $item) {
            if (preg_match("/$item\$/i", $file['name'])) return false;
        }

        $fi = finfo_open(FILEINFO_MIME_TYPE);
        $mime = (string)finfo_file($fi, $file['tmp_name']);
        $mimeList = ["image", "video", "audio"];
        $a = 0;
        foreach ($mimeList as $item) {
            if (strpos($mime, $item) !== false) $a = 1;
        }
        if ($a === 0) return false;

        return true;
    }

    public function getPathFile($idUser)
    {
        return "/../files/{$idUser}";
    }

    public function copyFile($path, $file, $fileName, $fileFormat)
    {
        if (!file_exists(PUBLIC_DIR . $path)) {
            mkdir(PUBLIC_DIR . $path);
        }

        $newFile = PUBLIC_DIR . $path . "/{$fileName}.{$fileFormat}";
        if (file_exists($newFile)) {
            $files = scandir(PUBLIC_DIR . $path);
            $arr = [];
            foreach ($files as $name) {
                if (preg_match("/^{$fileName}(\([0-9]\))?.{$fileFormat}$/", $name, $matches)) {
                    $arr[] = $matches[0];
                }
            }
            $length = count($arr);
            $name = "{$fileName}({$length}).{$fileFormat}";
            move_uploaded_file($file['tmp_name'], PUBLIC_DIR . $path . "/{$name}");
            $name = "{$fileName}({$length})";
        } else {
            $name = $fileName;
            move_uploaded_file($file['tmp_name'], $newFile);
        }

        if (file_exists(PUBLIC_DIR . $path . "/{$name}.{$fileFormat}")) {
            return $name;
        }
        return false;
    }

    public function changeFile($data)
    {
        $file = App::call()->fileRepository->newEntity($data);
        App::call()->fileRepository->save($file);
    }

    public function renameFile(array $file, $newName)
    {
        $item = PUBLIC_DIR . $file["path"] . "/{$file["name"]}.{$file["filename_extension"]}";
        $newItem = PUBLIC_DIR . $file["path"] . "/{$newName}.{$file["filename_extension"]}";

        if (file_exists($item)) {
            rename($item, $newItem);
        }

        if (file_exists($newItem)) {
            return true;
        }
        return false;
    }

    public function deleteFile($file)
    {
        $item = PUBLIC_DIR . $file["path"] . "/{$file["name"]}.{$file["filename_extension"]}";

        if (file_exists($item)) {
            unlink($item);
        }

        if (!file_exists($item)) {
            return true;
        }
        return false;
    }
}