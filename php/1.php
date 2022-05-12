<?php

namespace Manager;

use Data\DB;

class User
{
    const LIMIT = 10;

    /**
     * Возвращает пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    function getUsers(int $ageFrom): array
    {
        return \Gateway\User::getInstance()->getUsers($ageFrom, self::LIMIT);
    }

    /**
     * Возвращает пользователей по списку имен.
     * @param array $names
     * @return array
     */
    public static function getByNames(array $names): array
    {
        $users = [];
        $instance = \Gateway\User::getInstance();
        foreach ($names as $name) { //тут оптимальнее было бы одним запросом все выбирать
            $users[] = $instance->user($name);
        }

        return $users;
    }

    /**
     * Добавляет пользователей в базу данных.
     * @param $users
     * @return array
     */
    public function users($users): array
    {
        $ids = [];
        $instance = \Gateway\User::getInstance();
        $connection = DB::getInstance()->getConnection();
        $connection->beginTransaction();
        foreach ($users as $user) {
            try {
                $instance->add($user['name'], $user['lastName'], $user['age']);
                $connection->commit();
                $ids[] = $connection->lastInsertId();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }

        return $ids;
    }
}