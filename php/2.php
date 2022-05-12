<?php

namespace Gateway;

use Data\DB;
use PDO;

class User
{
    /**
     * @var DB
     */
    private $db;

    /**
     * @var self
     */
    private static $instance;

    protected function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Реализация singleton
     * @return self
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            $db = DB::getInstance();
            self::$instance = new self($db);
        }

        return self::$instance;
    }

    /**
     * Возвращает список пользователей старше заданного возраста.
     * @param int $ageFrom
     * @param int $limit
     * @return array
     */
    public function getUsers(int $ageFrom, int $limit): array
    {
        $stmt = $this->db->getConnection()->prepare("SELECT `id`, `name`, `lastName`, `from`, `age`, `settings` FROM Users WHERE age > :ageFrom LIMIT :limit",
            ['ageFrom' => $ageFrom, 'limit' => $limit]);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $settings = json_decode($row['settings']);
            $users[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'lastName' => $row['lastName'],
                'from' => $row['from'],
                'age' => $row['age'],
                'key' => $settings['key'],
            ];
        }

        return $users;
    }

    /**
     * Возвращает пользователя по имени.
     * @param string $name
     * @return array
     */
    public function user(string $name): array
    {
        $stmt = $this->db->getConnection()->prepare("SELECT `id`, `name`, `lastName`, `from`, `age`, `settings` FROM Users WHERE name = :name", ['name' => $name]);
        $stmt->execute();
        $user_by_name = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'id' => $user_by_name['id'],
            'name' => $user_by_name['name'],
            'lastName' => $user_by_name['lastName'],
            'from' => $user_by_name['from'],
            'age' => $user_by_name['age'],
        ];
    }

    /**
     * Добавляет пользователя в базу данных.
     * @param string $name
     * @param string $lastName
     * @param int $age
     * @return string
     */
    public function add(string $name, string $lastName, int $age): string
    {
        $connection = $this->db->getConnection();
        $sth = $connection->prepare("INSERT INTO Users (`name`, `lastName`, `age`) VALUES (:name, :age, :lastName)");
        $sth->execute([':name' => $name, ':age' => $age, ':lastName' => $lastName]);

        return $connection->lastInsertId();
    }
}