<?php
namespace Data;

use PDO;

class DB {

    CONST DSN = 'mysql:dbname=db;host=127.0.0.1';
    CONST USER = 'dbuser';
    CONST PASSWORD = 'dbpass';

    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var self
     */
    private static $instance;


    private function __construct()
    {
        $this->connection = new PDO(self::DSN, self::USER, self::PASSWORD);
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        if(!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}