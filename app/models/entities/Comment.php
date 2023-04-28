<?php

namespace App\models\entities;

use DateTime;

class Comment
{
    public int $user_id;
    public string $comment;
    public int $img_id;
    public $created;

    public function __construct($entity = null)
    {
        $this->user_id = $entity->user_id;
        $this->comment = $entity->comment;
        $this->img_id = $entity->img_id;
        $this->created = time();
    }
}