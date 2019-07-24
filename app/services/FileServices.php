<?php


namespace App\services;


use App\main\App;

class FileServices
{
    public function copyFile($id_user, $item, $folder_name = "")
    {
        $path = "/../files/{$id_user}";
        if(!empty($folder_name)){
            $path = "/../files/{$id_user}/{$folder_name}";
        }


        if (!file_exists(PUBLIC_DIR . $path)) {
            mkdir(PUBLIC_DIR . $path);
        }

        $file = PUBLIC_DIR . $path . "/{$item['name']}";
        copy($item['tmp_name'], $file);

        return $path;
    }

    public function changeFile($data)
    {
        $file = App::call()->fileRepository->newEntity($data);
        App::call()->fileRepository->save($file);
    }

    public function renameFile($path, $name, $fne, $newName)
    {
        if (file_exists(PUBLIC_DIR . $path . "/{$name}.{$fne}")) {
            rename(PUBLIC_DIR . $path . "/{$name}.{$fne}", PUBLIC_DIR . $path . "/{$newName}.{$fne}");
        }

        if(file_exists(PUBLIC_DIR . $path . "/{$newName}.{$fne}")){
            return true;
        } else{
            return false;
        }
    }

    public function deleteFile($path, $name, $fne)
    {
        $file = PUBLIC_DIR . $path. "/{$name}.{$fne}";

        if (file_exists($file)) {
            unlink($file);
        }

        if(!file_exists($file)){
            return true;
        } else{
            return false;
        }
    }
}