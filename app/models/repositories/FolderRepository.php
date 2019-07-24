<?php

namespace App\models\repositories;

use App\models\entities\Folder;

/**
 * Class FolderRepository
 * @package App\models
 *
 * @method Folder getOne($id)
 */
class FolderRepository extends Repository
{
    public function getTableName(): string
    {
        return "folder";
    }

    protected function getEntityClass()
    {
        return Folder::class;
    }

}