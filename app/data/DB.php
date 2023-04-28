<?php

namespace App\data;

use RedBeanPHP\R;
use RedBeanPHP\RedException;

try {
    R::setup('sqlite:' . DATA . 'gallary.sqlite');
    if (!R::testConnection()) {
        throw new RedException('No connection');
    }
} catch (RedException $e) {
    exit(var_dump($e));
}

class DB
{
    public static function findAll(string $table)
    {
        $table = self::testInput($table);
        return R::findAll($table);
    }

    public static function getAll(string $sql , int $id = \null)
    {
        return isset($id) ? R::getAll($sql, [$id]) : R::getAll($sql);
    }

    public static function get(string $table, string $id, string $sql = 'id = ?')
    {
        $table = self::testInput($table);
        return R::findOne($table, $sql, [$id]);
    }
    public static function create(object $entity, string $table)
    {
        // \var_dump($entity);
        // die;
        $bean = R::dispense($table);
        foreach ($entity as $k => $v) {
            $bean->$k = $v;
        }

        try {
            $id = R::store($bean);
        } catch (RedException $e) {
            R::rollback();
            die(json_encode('Ошибка загрузки или файл существует.'));
        }

        return $id;
    }

    public static function update(object $obj, string $table)
    {
        $bean = R::load($table, $obj->id);
        $bean->user_hash = $obj->user_hash;
        $bean->user_ip = $obj->user_ip;
        $bean->updated = $obj->updated;

        $id = R::store($bean);

        return $id;
    }

    public static function delete(string $table, $id, $sqlSnippet = 'id = ?')
    {

        // \var_dump($id);
        $id = R::hunt($table, $sqlSnippet, [$id]);
        return $id;
    }

    // public static function dropTable(string $table)
    // {
    //     // TODO
    // }

    private static function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data);
    }
}
