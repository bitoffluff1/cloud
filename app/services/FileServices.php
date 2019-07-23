<?php


namespace App\services;


use App\main\App;

class FileServices
{
    public function copyFile($id_user, $item)
    {
        if(!file_exists(PUBLIC_DIR . "/../files/{$id_user}")){
            mkdir(PUBLIC_DIR . "/../files/{$id_user}");
        }

        $file = PUBLIC_DIR . "/../files/{$id_user}/{$item['name']}";
        copy($item['tmp_name'], $file);
    }

    public function changeFile($data)
    {
        $file = App::call()->fileRepository->newEntity($data);
        App::call()->fileRepository->save($file);
    }

    public function renameFile($id_user, $name, $fne, $newName)
    {
        $path = PUBLIC_DIR . "/../files/{$id_user}";

        if(file_exists($path . "/{$name}.{$fne}")){
            rename($path . "/{$name}.{$fne}", $path . "/{$newName}.{$fne}" );
        }
    }

    public function deleteFile($id_user, $name, $fne){
        $file = PUBLIC_DIR . "/../files/{$id_user}/{$name}.{$fne}";

        if(file_exists($file)){
            unlink($file);
        }
    }
}