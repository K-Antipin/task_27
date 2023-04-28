<?php

namespace App\models\entities;

use DateTime;

class User
{
    public string $name;
    public string $email;
    public string $password;
    public string $user_hash;
    public string $user_ip;
    public string $role;
    public $created;
    public function __construct($entity = null)
    {
        $this->name = $entity->name;
        $this->email = $entity->email;
        $this->password = $entity->password;
        $this->user_hash = $entity->user_hash;
        $this->user_ip = $entity->user_ip;
        $this->created = time();
        self::setRole($entity->role);
    }
    public function setRole($role  = null)
    {
        isset($role) ? $this->role = $role : $this->role = 'user';
    }
}