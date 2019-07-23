<?php

namespace App\models\repositories;

use App\models\entities\File;

/**
 * Class FileRepository
 * @package App\models
 *
 * @method File getOne($id)
 */
class FileRepository extends Repository
{
    public function getTableName(): string
    {
        return "files";
    }

    protected function getEntityClass()
    {
        return File::class;
    }

}