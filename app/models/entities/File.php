<?php

namespace App\models\entities;

use DateTime;

class File
{
    public string $file_name;
    public int $user_id;
    public $created;

    public function __construct($entity = null)
    {
        $this->file_name = $entity->file_name;
        $this->user_id = $entity->user_id;
        $this->created = time();
    }
}